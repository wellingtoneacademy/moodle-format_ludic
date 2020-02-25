<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains main class for the course format Ludic
 *
 * @package   format_ludic
 * @copyright 2020 Edunao SAS (contact@edunao.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Course lib.
require_once($CFG->dirroot . '/course/format/lib.php');

require_once($CFG->dirroot . '/course/format/ludic/classes/data_api.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/database_api.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/context_helper.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/model.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/course.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/section.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/course_module.php');

// Renderable.
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/popup.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/item.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/section.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/course_module.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/form.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/form_element.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/hidden_form_element.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/text_form_element.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/number_form_element.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/checkbox_form_element.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/textarea_form_element.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/select_form_element.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/filepicker_form_element.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/selection_popup_form_element.php');
require_once($CFG->dirroot . '/course/format/ludic/classes/renderers/renderable/modchooser.php');

// Controller.
require_once $CFG->dirroot . '/course/format/ludic/classes/controllers/front_controller_interface.php';
require_once($CFG->dirroot . '/course/format/ludic/classes/controllers/front_controller.php');
require_once $CFG->dirroot . '/course/format/ludic/classes/controllers/controller_base.php';
require_once $CFG->dirroot . '/course/format/ludic/classes/controllers/section.controller.php';
require_once $CFG->dirroot . '/course/format/ludic/classes/controllers/skin.controller.php';

// Form.
require_once $CFG->dirroot . '/course/format/ludic/classes/forms/form.php';
require_once $CFG->dirroot . '/course/format/ludic/classes/forms/section_form.php';
require_once $CFG->dirroot . '/course/format/ludic/classes/forms/activity_skin_score_form.php';
require_once $CFG->dirroot . '/course/format/ludic/classes/forms/form_element.php';

/**
 * Main class for the Ludic course format
 *
 * @package   format_ludic
 * @copyright 2020 Edunao SAS (contact@edunao.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_ludic extends \format_base {

    /**
     * Returns true if this course format uses sections
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport          = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * ludic format uses the following options:
     * - ludic_config
     * - ludic_sharing_key
     *
     * @param bool $foreditform
     * @return array of options
     * @throws \coding_exception
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;

        if ($courseformatoptions === false) {
            $courseformatoptions = [
                    'ludic_config'         => [
                            'type'         => PARAM_RAW, 'label' => get_string('ludicconfiglabel', 'format_ludic'),
                            'element_type' => 'hidden'
                    ], 'ludic_sharing_key' => [
                            'type'         => PARAM_RAW, 'label' => get_string('ludicsharingkeylabel', 'format_ludic'),
                            'element_type' => 'hidden',
                    ],
            ];
        }

        return $courseformatoptions;
    }
}

/**
 * Serve the files from the MYPLUGIN file areas
 *
 * @param \stdClass $course the course object
 * @param \stdClass $cm the course module object
 * @param \stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function format_ludic_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_COURSE) {
        return false;
    }

    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    require_login($course, true);

    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.

    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains items of the filepath
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'course', 'section', $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}
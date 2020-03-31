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
 * Activity skin inline.
 *
 * @package   format_ludic
 * @copyright 2020 Edunao SAS (contact@edunao.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_ludic\coursemodule;

defined('MOODLE_INTERNAL') || die();

class inline extends \format_ludic\skin {

    /**
     * @return \stdClass
     */
    public function get_edit_image() {
        global $CFG;
        return (object) [
                'imgsrc' => $CFG->wwwroot . "/course/format/ludic/pix/inline.png",
                'imgalt' => 'In page'
        ];
    }

    /**
     * Return an instance of this class.
     *
     * @return inline
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    static public function get_instance() {
        return new self((object) [
                'id'          => FORMAT_LUDIC_CM_SKIN_INLINE_ID,
                'location'    => 'coursemodule',
                'type'        => 'inline',
                'title'       => get_string('cm-skin-inline-title', 'format_ludic'),
                'description' => get_string('cm-skin-inline-description', 'format_ludic')
        ]);
    }

    /**
     * This skin does not require grade.
     *
     * @return false
     */
    public function require_grade() {
        return false;
    }


    /**
     * @inheritDoc
     */
    public function get_images_to_render() {
        // TODO: Implement get_images_to_render() method.
    }

    /**
     * @inheritDoc
     */
    public function get_texts_to_render() {
        // TODO: Implement get_texts_to_render() method.
    }
}


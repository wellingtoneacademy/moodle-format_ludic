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
 * Skin controller class.
 *
 * @package   format_ludic
 * @copyright 2020 Edunao SAS (contact@edunao.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_ludic;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/ludic/lib.php');

class skin_controller extends controller_base {

    /**
     * Execute an action.
     *
     * @return false|string
     * @throws \moodle_exception
     */
    public function execute() {
        $action = $this->get_param('action');
        switch ($action) {
            case 'get_properties' :
                $skinid = $this->get_param('id', PARAM_INT);
                return $this->get_properties($skinid);
            case 'get_children' :
                $skinid = $this->get_param('id', PARAM_INT);
                return $this->get_children($skinid);
            case 'get_description' :
                $skinid = $this->get_param('id', PARAM_INT);
                return $this->get_description($skinid);
            case 'get_section_skin_selector' :
                $selectedskinid = $this->get_param('selectedid', PARAM_INT);
                return $this->get_section_skin_selector($selectedskinid);
            // Default case if no parameter is necessary.
            default :
                return $this->$action();
        }
    }

    /**
     * TODO implements.
     *
     * @return false|string
     * @throws \moodle_exception
     */
    public function get_cm_skin_selector() {
        global $PAGE;
        $this->set_context();
        $renderer = $PAGE->get_renderer('format_ludic');
        // TODO $skins = $this->get_cm_skins();.
        $title   = 'CM SKIN SELECTION';
        $content = $renderer->render_from_template('format_ludic/test', []);
        $popup   = new \format_ludic_popup($title, $content);
        return $renderer->render_popup($popup);
    }

    public function get_section_skin_selector($selectedskinid) {
        global $PAGE;
        $renderer = $PAGE->get_renderer('format_ludic');
        $skins    = $this->contexthelper->get_section_skins();

        $content = '';
        foreach ($skins as $skin) {
            if (isset($selectedskinid) && $selectedskinid == $skin->id) {
                $skin->selected = true;
            }
            $skin->propertiesaction = 'get_description';
            $content .= $renderer->render_skin($skin);
        }

        return $renderer->render_container_items('section-skin', $content);
    }

    /**
     * TODO implements.
     *
     * @param $skinid
     * @return string
     */
    public function get_children($skinid) {
        return 'NO CHILDREN FOR SKIN => ' . $skinid;
    }

    /**
     * TODO implements.
     *
     * @param $skinid
     * @return string
     */
    public function get_properties($skinid) {
        return 'SKIN ' . $skinid . ' PROPERTIES';
    }


    /**
     * TODO implements.
     *
     * @param $skinid
     * @return string
     */
    public function get_description($skinid) {
        $skin = skin::get_by_id($skinid);
        return $skin->description;
    }

}

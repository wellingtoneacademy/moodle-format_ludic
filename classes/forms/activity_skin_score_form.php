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
 * This file contains main class for the course format Ludic form
 *
 * @package   format_ludic
 * @copyright 2020 Edunao SAS (contact@edunao.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_ludic;

class activity_skin_score_form extends form {

    public function __construct($id = null) {
        parent::__construct('activity_skin_score', $id);
        if ($id) {
            $dataapi = $this->contexthelper->get_data_api();
            $this->object = $dataapi->get_skin_by_id($id);
        }
    }

    public function get_definition() {
        $elements   = [];
        $elements[] = [
                'name' => 'linear_score_part',
                'type' => 'number',
                'value' => 1,
                'attributes' => [
                        'min' => 0,
                        'max' => 100
                ]
        ];
        return $elements;
    }
}
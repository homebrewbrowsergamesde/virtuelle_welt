<?php
/* Copyright (C) 2012  Stephan Kreutzer
 *
 * This file is part of Welt.
 *
 * Welt is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * Welt is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Welt.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * @file $/editor/editor.inc.php
 * @brief Functions for the editor.
 * @author Stephan Kreutzer
 * @since 2012-02-16
 */



/**
 * @brief Checks if a given time string is numerical
 *     valid.
 * @param[in] $time "hh:mm:ss" expected, but leading zeros
 *     are not required.
 */
function checktime($time)
{
    $validTime = true;

    $time = explode(":", $time, 3);

    if (count($time) != 3)
    {
        $validTime = false;
    }

    if ($validTime === true)
    {
        if (is_numeric($time[0]) === true)
        {
            if ($time[0] < 0 || $time[0] > 23)
            {
                $validTime = false;
            }
        }
        else
        {
            $validTime = false;
        }
    }

    if ($validTime === true)
    {
        if (is_numeric($time[1]) === true &&
            is_numeric($time[2]) === true)
        {
            if ($time[1] < 0 || $time[1] > 59 ||
                $time[2] < 0 || $time[2] > 59)
            {
                $validTime = false;
            }
        }
        else
        {
            $validTime = false;
        }
    }
    
    return $validTime;
}



?>
<?php
/* Copyright (C) 2011-2012  Stephan Kreutzer
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
 * @file $/editor/map_insert.php
 * @brief Insert a single object into the map.
 * @details Takes care if the objects placement causes an overlap over top
 *     and/or right corner.
 * @author Stephan Kreutzer
 * @since 2011-09-28
 */



require_once(dirname(__FILE__)."/../data/globals.inc.php");
require_once(dirname(__FILE__)."/../libraries/database.inc.php");



echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n".
     "\n".
     "\n".
     "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n".
     "\n".
     "\n".
     "  <head>\n".
     "\n".
     "\n".
     "      <title>Welt map insert</title>\n".
     "\n".
     "      <meta name=\"robots\" content=\"noindex, nofollow\" />\n".
     "      <meta http-equiv=\"expires\" content=\"1296000\" />\n".
     "      <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\" />\n".
     "\n".
     "\n".
     "  </head>\n".
     "\n".
     "\n".
     "  <body>\n".
     "\n".
     "\n".
     "      <div>\n";

if (isset($_GET['x']) != true ||
    isset($_GET['y']) != true ||
    isset($_GET['position_x']) != true ||
    isset($_GET['position_y']) != true ||
    isset($_GET['object']) != true ||
    isset($_GET['order_z']) != true ||
    isset($_GET['from']) != true ||
    isset($_GET['to']) != true)
{
    $objects = db_get_rows("SELECT `id`,\n".
                           "    `file`\n".
                           "FROM `objects`\n".
                           "WHERE 1\n".
                           "ORDER BY `file` ASC\n");

    if (is_array($objects) === true)
    {
        echo "        <form action=\"map_insert.php\" method=\"get\">\n".
             "          <input name=\"x\" type=\"text\" size=\"3\" maxlength=\"20\" /> x map coordinate<br />\n".
             "          <input name=\"y\" type=\"text\" size=\"3\" maxlength=\"20\" /> y map coordinate<br />\n".
             "          <select name=\"object\" size=\"1\">\n";

        foreach ($objects as $object)
        {
            echo "            <option value=\"".$object['id']."\">".$object['file']."</option>\n";
        }

        echo "          </select> file<br />\n".
             "          <input name=\"position_x\" type=\"text\" size=\"3\" maxlength=\"20\" /> x pixel position (between 0 and ".(VIEW_X - 1).")<br />\n".
             "          <input name=\"position_y\" type=\"text\" size=\"3\" maxlength=\"20\" /> y pixel position (between 0 and ".(VIEW_Y - 1).")<br />\n".
             "          <input name=\"order_z\" type=\"text\" size=\"3\" maxlength=\"20\" /> z order<br />\n".
             "          <input name=\"from\" type=\"text\" size=\"8\" maxlength=\"8\" /> from (between 00:00:00 and 23:59:59)<br />\n".
             "          <input name=\"to\" type=\"text\" size=\"8\" maxlength=\"8\" /> to (between 00:00:00 and 24:00:00)<br />\n".
             "          <input type=\"submit\" value=\"insert\" /><br />\n".
             "        </form>\n";
    }
}
else
{
    $validData = true;

    if (is_numeric($_GET['x']) !== true)
    {
        echo "x not numeric.<br />";
        $validData = false;
    }

    if (is_numeric($_GET['y']) !== true)
    {
        echo "y not numeric.<br />";
        $validData = false;
    }

    if (is_numeric($_GET['object']) !== true)
    {
        echo "object id is not numeric.<br />";
        $validData = false;
    }

    if (is_numeric($_GET['position_x']) !== true)
    {
        echo "x pixel position not numeric.<br />";
        $validData = false;
    }

    if (is_numeric($_GET['position_y']) !== true)
    {
        echo "y pixel position not numeric.<br />";
        $validData = false;
    }

    if (is_numeric($_GET['order_z']) !== true)
    {
        echo "z order is not numeric.<br />";
        $validData = false;
    }

    require_once("editor.inc.php");

    if (checktime($_GET['from']) !== true)
    {
        echo "from time not valid.<br />";
        $validData = false;
    }
    else
    {
        $timeFrom = explode(":", $_GET['from'], 3);
        $timeFrom = date("H:i:s", mktime($timeFrom[0], $timeFrom[1], $timeFrom[2]));
    }

    $timeTo = "";

    if ($_GET['to'] === "24:00:00" ||
        $_GET['to'] === "24:0:00" ||
        $_GET['to'] === "24:00:0" ||
        $_GET['to'] === "24:0:0")
    {
         // Exception for MySQL TIME data type.
         $timeTo = "24:00:00";
    }
    else
    {
        if (checktime($_GET['to']) !== true)
        {
            echo "to time not valid.<br />";
            $validData = false;
        }
        else
        {
            $timeTo = explode(":", $_GET['to'], 3);
            $timeTo = date("H:i:s", mktime($timeTo[0], $timeTo[1], $timeTo[2]));
        }
    }

    if ($validData === true)
    {
        insertMap($_GET['x'],
                  $_GET['y'],
                  $_GET['position_x'],
                  $_GET['position_y'],
                  $_GET['object'],
                  $_GET['order_z'],
                  $timeFrom,
                  $timeTo);
    }
}

echo "        <div>\n".
     "          <a href=\"index.php\">menu</a>\n".
     "        </div>\n".
     "      </div>\n".
     "\n".
     "\n".
     "  </body>\n".
     "\n".
     "\n".
     "</html>\n";



function insertMap($x, $y, $position_x, $position_y, $objectID, $order_z, $from, $to)
{
    $file = db_get_rows("SELECT `file`\n".
                        "FROM `objects`\n".
                        "WHERE `id`=".$objectID."\n");

    if (is_array($file) !== true)
    {
        echo "no object.<br />";
        return -1;
    }
    else
    {
        $file = $file[0]['file'];
    }

    $filePath = dirname(__FILE__)."/../resources/".$file;
    $size = @getimagesize($filePath);

    if ($size == false)
    {
        echo "file not found.<br />";
        return -2;
    }

    if ($position_x >= VIEW_X ||
        $position_x < 0 ||
        $position_y >= VIEW_Y ||
        $position_y < 0)
    {
        echo "out of range.<br />";
        return -3;
    }

    if ($size[0] > VIEW_X ||
        $size[0] <= 0 ||
        $size[1] > VIEW_Y ||
        $size[1] <= 0)
    {
        echo "bad size.<br />";
        return -4;
    }


    $id = db_insert("INSERT INTO `map` (`id`,\n".
                    "    `map_x`,\n".
                    "    `map_y`,\n".
                    "    `object_id`,\n".
                    "    `position_x`,\n".
                    "    `position_y`,\n".
                    "    `order_z`,\n".
                    "    `from`,\n".
                    "    `to`,\n".
                    "    `visible`)\n".
                    "VALUES (NULL,\n".
                    "    ".$x.",\n".
                    "    ".$y.",\n".
                    "    ".$objectID.",\n".
                    "    ".$position_x.",\n".
                    "    ".$position_y.",\n".
                    "    ".$order_z.",\n".
                    "    '".$from."',\n".
                    "    '".$to."',\n".
                    "    NULL)");

    echo "regular id: ".$id."<br />";

    // -1 for $size[0] if used in positioning context because
    // the image size is not zero-based but the coordinate system is.
    if ($position_x + ($size[0] - 1) >= VIEW_X)
    {
        // x over right.

        $id = db_insert("INSERT INTO `map` (`id`,\n".
                        "    `map_x`,\n".
                        "    `map_y`,\n".
                        "    `object_id`,\n".
                        "    `position_x`,\n".
                        "    `position_y`,\n".
                        "    `order_z`,\n".
                        "    `from`,\n".
                        "    `to`,\n".
                        "    `visible`)\n".
                        "VALUES (NULL,\n".
                        "    ".($x + 1).",\n".
                        "    ".$y.",\n".
                        "    ".$objectID.",\n".
                        "    ".($position_x - VIEW_X).",\n".
                        "    ".$position_y.",\n".
                        "    ".$order_z.",\n".
                        "    '".$from."',\n".
                        "    '".$to."',\n".
                        "    NULL)");

        echo "x over right. id is: ".$id."<br />";
    }

    // -1 for $size[1] if used in positioning context because
    // the image size is not zero-based but the coordinate system is.
    if ($position_y - ($size[1] - 1) < 0)
    // Alternative: ($size[1] - 1) > $position_y (must have at least the space
    // for the entire object).
    {
        // y over top.

        $id = db_insert("INSERT INTO `map` (`id`,\n".
                        "    `map_x`,\n".
                        "    `map_y`,\n".
                        "    `object_id`,\n".
                        "    `position_x`,\n".
                        "    `position_y`,\n".
                        "    `order_z`,\n".
                        "    `from`,\n".
                        "    `to`,\n".
                        "    `visible`)\n".
                        "VALUES (NULL,\n".
                        "    ".$x.",\n".
                        "    ".($y - 1).",\n".
                        "    ".$objectID.",\n".
                        "    ".$position_x.",\n".
                        "    ".(VIEW_Y + $position_y).",\n".
                        "    ".$order_z.",\n".
                        "    '".$from."',\n".
                        "    '".$to."',\n".
                        "    NULL)");

        echo "y over top. id is: ".$id."<br />";
    }

    // -1 for $size elements if used in positioning context because
    // the image size is not zero-based but the coordinate system is.
    if ($position_x + ($size[0] - 1) >= VIEW_X &&
        $position_y - ($size[1] - 1) < 0)
    {
        // x over right and y over top.

        $id = db_insert("INSERT INTO `map` (`id`,\n".
                        "    `map_x`,\n".
                        "    `map_y`,\n".
                        "    `object_id`,\n".
                        "    `position_x`,\n".
                        "    `position_y`,\n".
                        "    `order_z`,\n".
                        "    `from`,\n".
                        "    `to`,\n".
                        "    `visible`)\n".
                        "VALUES (NULL,\n".
                        "    ".($x + 1).",\n".
                        "    ".($y - 1).",\n".
                        "    ".$objectID.",\n".
                        "    ".($position_x - VIEW_X).",\n".
                        "    ".(VIEW_Y + $position_y).",\n".
                        "    ".$order_z.",\n".
                        "    '".$from."',\n".
                        "    '".$to."',\n".
                        "    NULL)");

        echo "x over right and y over top. id is: ".$id."<br />";
    }
}



?>
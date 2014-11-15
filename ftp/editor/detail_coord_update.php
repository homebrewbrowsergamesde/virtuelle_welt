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
 * @file $/editor/detail_coord_update.php
 * @brief Modify a single coord on the detail view.
 * @author Stephan Kreutzer
 * @since 2012-02-21
 */



if (isset($_GET['coord_id']) !== true)
{
    echo "no coord id.<br />";
    exit(-1);
}

if (is_numeric($_GET['coord_id']) !== true)
{
    echo "invalid coord id.<br />";
    exit(-1);
}



require_once(dirname(__FILE__)."/../libraries/database.inc.php");



if (isset($_GET['order_z']) === true &&
    isset($_GET['from']) === true &&
    isset($_GET['to']) === true &&
    isset($_GET['coords']) === true &&
    isset($_GET['href']) === true &&
    isset($_GET['alt']) === true &&
    isset($_GET['title']) === true)
{
    $validData = true;

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

    $coords = explode(",", $_GET['coords']);
    $coord_count = count($coords);
    
    if (count($coords) % 2 != 0)
    {
        echo "one coord missing.<br />";
        $validData = false;
    }

    for ($i = 0; $i < $coord_count; $i++)
    {
        if (is_numeric($coords[$i]) !== true)
        {
            echo "coord nr. ".($i + 1)." is not numeric.<br />";
            $validData = false;
        }
    }

    $href = db_prepare_string($_GET['href']);
    
    if ($href == false)
    {
        echo "invalid link.<br />";
        $validData = false;
    }

    $alt = db_prepare_string($_GET['alt']);

    if ($alt == false)
    {
        echo "invalid alternative text.<br />";
        $validData = false;
    }

    $title = db_prepare_string($_GET['title']);

    if ($title == false)
    {
        echo "invalid title.<br />";
        $validData = false;
    }

    if ($validData === true)
    {
        db_set("UPDATE `detail_coords`\n".
               "SET `order_z`=".$_GET['order_z'].",\n".
               "    `from`='".$timeFrom."',\n".
               "    `to`='".$timeTo."',\n".
               "    `coords`='".$_GET['coords']."',\n".
               "    `href`='".$href."',\n".
               "    `alt`='".$alt."',\n".
               "    `title`='".$title."',\n".
               "    `visible`=NULL\n".
               "WHERE `id`=".$_GET['coord_id']."\n");
    }
    else
    {
        exit(-4);
    }
}



$coord = db_get_rows("SELECT `name`,\n".
                     "    `detail_id`,\n".
                     "    `order_z`,\n".
                     "    `from`,\n".
                     "    `to`,\n".
                     "    `visible`,\n".
                     "    `coords`,\n".
                     "    `href`,\n".
                     "    `alt`,\n".
                     "    `title`\n".
                     "FROM `detail_coords`\n".
                     "WHERE `id`=".$_GET['coord_id']."\n");

if (is_array($coord) === true)
{
    $coord = $coord[0];
}
else
{
    echo "can't get coord data.<br />";
    exit(-2);
}



$name = "";
$time = date("H:i:s");

if (isset($_GET['name']) === true)
{
    if ($_GET['name'] == $coord['name'])
    {
        $name = $coord['name'];
    }
    else
    {
        echo "given detail name and detail name of the coord data do not match.<br />";
        exit(-3);
    }
}
else
{
    echo "no detail name.<br />";
    exit(-3);
}

if (isset($_GET['id']) === true)
{
    if ($_GET['id'] != $coord['detail_id'])
    {
        echo "given detail id and detail id of the coord data do not match.<br />";
        exit(-3);
    }
}
else
{
    echo "no detail id.<br />";
    exit(-3);
}

if (isset($_GET['time']) === true)
{
    require_once("editor.inc.php");

    if (checktime($_GET['time']) === true)
    {
        $time = explode(":", $_GET['time'], 3);
        $time = date("H:i:s", mktime($time[0], $time[1], $time[2]));
    }
}

$coordTime = $coord['from'];

if (isset($_GET['coord_time']) === true)
{
    require_once("editor.inc.php");

    if (checktime($_GET['coord_time']) === true)
    {
        $coordTime = explode(":", $_GET['coord_time'], 3);
        $coordTime = date("H:i:s", mktime($coordTime[0], $coordTime[1], $coordTime[2]));
    }
}



require_once("../libraries/view_generator.inc.php");

$success = generateDetail($name, $coordTime);

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
     "      <title>Welt detail coord update</title>\n".
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
     "      <div>\n".
     "        <form action=\"detail_coord_update.php\" method=\"get\">\n".
     "          <input name=\"coord_time\" type=\"text\" size=\"8\" maxlength=\"8\" value=\"".$coordTime."\" /> time<br />\n".
     "          <input name=\"name\" type=\"hidden\" value=\"".$name."\" />\n".
     "          <input name=\"time\" type=\"hidden\" value=\"".$time."\" />\n".
     "          <input name=\"id\" type=\"hidden\" value=\"".$coord['detail_id']."\" />\n".
     "          <input name=\"coord_id\" type=\"hidden\" value=\"".$_GET['coord_id']."\" />\n".
     "          <input type=\"submit\" value=\"submit\" /><br />\n".
     "        </form>\n";

if ($success == 0)
{
    $coord_xy = explode(",", $coord['coords']);
    $coord_count = count($coord_xy);

    $validCoord = true;

    if ($coord_count % 2 != 0)
    {
        echo "one coord missing.<br />";
        $validCoord = false;
    }

    if ($coord_count < 6)
    {
        echo "at least 3 coord pairs needed for polygon.<br />";
        $validCoord = false;
    }

    if ($validCoord === true)
    {
       $position = db_get_rows("SELECT `objects`.`file`,\n".
                                "    `detail`.`position_x`,\n".
                                "    `detail`.`position_y`\n".
                                "FROM `detail` JOIN `objects`\n".
                                "ON `detail`.`object_id`=`objects`.`id`\n".
                                "WHERE `detail`.`id`=".$coord['detail_id']."\n");

        if (is_array($position) !== true)
        {
            echo "can't get position info.<br />";
        }
        else
        {
            $position = $position[0];

            $filePath = dirname(__FILE__)."/../resources/".$position['file'];
            $size = getimagesize($filePath);

            $filePath = "../cache/".$name.".png";
            $image = @imagecreatefrompng($filePath);

            if ($image == false)
            {
                echo "image file not found.<br />";
            }
            else
            {
                // Default color to draw the polygons area line.
                $lineColor = imagecolorallocate($image, 255, 0, 0);

                $last_x = $coord_xy[0] + $position['position_x'];
                // -1 for $size[1] if used in positioning context because
                // the image size is not zero-based but the coordinate system is.
                $last_y = ($position['position_y'] - ($size[1] - 1)) + $coord_xy[1];

                for ($i = 2; $i < $coord_count; $i += 2)
                {
                    $current_x = $coord_xy[$i] + $position['position_x'];
                    // -1 for $size[1] if used in positioning context because
                    // the image size is not zero-based but the coordinate system is.
                    $current_y = ($position['position_y'] - ($size[1] - 1)) + $coord_xy[$i+1];

                    imageline($image, $last_x, $last_y, $current_x, $current_y, $lineColor);

                    $last_x = $current_x;
                    $last_y = $current_y;
                }

                // Close the polygon with line to start position.

                $current_x = $coord_xy[0] + $position['position_x'];
                // -1 for $size[1] if used in positioning context because
                // the image size is not zero-based but the coordinate system is.
                $current_y = ($position['position_y'] - ($size[1] - 1)) + $coord_xy[1];

                imageline($image, $last_x, $last_y, $current_x, $current_y, $lineColor);

                imagepng($image, "../cache/temp_editor.png");
                imagedestroy($image);

                echo "        <img src=\"../cache/temp_editor.png?dummy=".md5(uniqid(rand(), true))."\" border=\"1\" alt=\"area\" />\n";
            }
        }
    }
}

echo "        <form action=\"detail_coord_update.php\" method=\"get\">\n".
     "          <input name=\"order_z\" type=\"text\" size=\"3\" maxlength=\"20\" value=\"".$coord['order_z']."\" /> z order<br />\n".
     "          <input name=\"from\" type=\"text\" size=\"8\" maxlength=\"8\" value=\"".$coord['from']."\" /> from (between 00:00:00 and 23:59:59)<br />\n".
     "          <input name=\"to\" type=\"text\" size=\"8\" maxlength=\"8\" value=\"".$coord['to']."\" /> to (between 00:00:00 and 24:00:00)<br />\n".
     "          <input name=\"coords\" type\"text\" size=\"80\" value=\"".$coord['coords']."\" /> coords (x and y coordinate pairs separated by comma)<br />\n".
     "          <input name=\"href\" type=\"text\" size=\"80\" maxlength=\"255\" value=\"".$coord['href']."\" /> link<br />\n".
     "          <input name=\"alt\" type=\"text\" size=\"80\" maxlength=\"255\" value=\"".$coord['alt']."\" /> alternative text<br />\n".
     "          <input name=\"title\" type=\"text\" size=\"80\" maxlength=\"255\" value=\"".$coord['title']."\" /> title<br />\n".
     "          <input name=\"id\" type=\"hidden\" value=\"".$_GET['id']."\" />\n".
     "          <input name=\"name\" type=\"hidden\" value=\"".$name."\" />\n".
     "          <input name=\"time\" type=\"hidden\" value=\"".$time."\" />\n".
     "          <input name=\"coord_id\" type=\"hidden\" value=\"".$_GET['coord_id']."\" />\n".
     "          <input name=\"coord_time\" type=\"hidden\" value=\"".$coordTime."\" />\n".
     "          <input type=\"submit\" value=\"update\" /><br />\n".
     "        </form>\n";

echo "        <div>\n".
     "          <a href=\"detail_update.php?name=".$name."&time=".$time."&id=".$coord['detail_id']."\">menu</a>\n".
     "        </div>\n".
     "      </div>\n".
     "\n".
     "\n".
     "  </body>\n".
     "\n".
     "\n".
     "</html>\n";



?>
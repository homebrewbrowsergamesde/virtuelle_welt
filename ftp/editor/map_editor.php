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
 * @file $/editor/map_editor.php
 * @brief Simple map editor.
 * @author Stephan Kreutzer
 * @since 2012-02-15
 */



$x = 0;
$y = 0;
$currentTime = date("H:i:s");

if (isset($_GET['x']) === true)
{
    if (is_numeric($_GET['x']) === true)
    {
        $x = $_GET['x'];
    }
}

if (isset($_GET['y']) === true)
{
    if (is_numeric($_GET['y']) === true)
    {
        $y = $_GET['y'];
    }
}

if (isset($_GET['time']) === true)
{
    require_once("editor.inc.php");

    if (checktime($_GET['time']) === true)
    {
        $currentTime = explode(":", $_GET['time'], 3);
        $currentTime = date("H:i:s", mktime($currentTime[0], $currentTime[1], $currentTime[2]));
    }
}

if (is_numeric($x) !== true)
{
    $x = 0;
}

if (is_numeric($y) !== true)
{
    $y = 0;
}



require_once(dirname(__FILE__)."/../libraries/database.inc.php");



if (isset($_GET['action']) === true)
{
    if ($_GET['action'] === "delete")
    {
        $success = true;

        if (isset($_GET['delete_id']) !== true)
        {
            $success = false;
        }

        if ($success === true)
        {
            if (is_numeric($_GET['delete_id']) !== true)
            {
                $success = false;
            }
        }

        if ($success === true)
        {
            db_set("DELETE FROM `map_coords`\n".
                   "WHERE `map_id`=".$_GET['delete_id']."\n");
            db_set("DELETE FROM `map`\n".
                   "WHERE `id`=".$_GET['delete_id']."\n");

            // Hard reset.

            require_once(dirname(__FILE__)."/../data/globals.inc.php");

            $image = imagecreatetruecolor(VIEW_X, VIEW_Y);
            imagepng($image, dirname(__FILE__)."/../cache/".$x."_".$y.".png");
            imagedestroy($image);

            db_set("UPDATE `map`\n".
                   "SET `visible`=NULL\n".
                   "WHERE `map_x`=".$x." AND\n".
                   "    `map_y`=".$y."\n");
        }
    }
}



require_once("../libraries/view_generator.inc.php");

$success = generateMap($x, $y, $currentTime);

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
     "      <title>Welt map editor</title>\n".
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

echo "        <form action=\"map_editor.php\" method=\"get\">\n".
     "          <input name=\"x\" type=\"text\" size=\"3\" maxlength=\"20\" value=\"".$x."\" /> x \n".
     "          <input name=\"y\" type=\"text\" size=\"3\" maxlength=\"20\" value=\"".$y."\" /> y \n".
     "          <input name=\"time\" type=\"text\" size=\"8\" maxlength=\"8\" value=\"".$currentTime."\" /> time<br />\n".
     "          <input type=\"submit\" value=\"submit\" /><br />\n".
     "        </form>\n";

if ($success == 0)
{
    echo "        <img src=\"../cache/".$x."_".$y.".png?dummy=".md5(uniqid(rand(), true))."\" border=\"1\" alt=\"Welt[".$x.",".$y."]\" />\n";
}

$map = db_get_rows("SELECT `map`.`id`,\n".
                   "    `objects`.`file`,\n".
                   "    `map`.`position_x`,\n".
                   "    `map`.`position_y`,\n".
                   "    `map`.`order_z`,\n".
                   "    `map`.`from`,\n".
                   "    `map`.`to`,\n".
                   "    `map`.`visible`\n".
                   "FROM `map` JOIN `objects`\n".
                   "ON `map`.`object_id`=`objects`.`id`\n".
                   "WHERE `map`.`map_x`=".$x." AND\n".
                   "    `map`.`map_y`=".$y."\n".
                   "ORDER BY `map`.`from` ASC,\n".
                   "    `map`.`order_z` DESC,\n".
                   "    `map`.`visible` ASC\n");

if (is_array($map) === true)
{
    echo "        <table border=\"1\">\n".
         "          <tr>\n".
         "            <th>object</th>\n".
         "            <th style=\"white-space:nowrap;\">x pixel</th>\n".
         "            <th style=\"white-space:nowrap;\">y pixel</th>\n".
         "            <th style=\"white-space:nowrap;\">z order</th>\n".
         "            <th>from</th>\n".
         "            <th>to</th>\n".
         "            <th>visible</th>\n".
         "            <th>action</th>\n".
         "          </tr>\n";

    foreach ($map as $object)
    {
        echo "          <tr>\n".
             "            <td style=\"white-space:nowrap;\">".$object['file']."</td>\n".
             "            <td>".$object['position_x']."</td>\n".
             "            <td>".$object['position_y']."</td>\n".
             "            <td>".$object['order_z']."</td>\n".
             "            <td>".$object['from']."</td>\n".
             "            <td>".$object['to']."</td>\n".
             "            <td>".$object['visible']."</td>\n".
             "            <td style=\"white-space:nowrap;\"><a href=\"map_update.php?x=".$x."&y=".$y."&time=".$currentTime."&id=".$object['id']."\">edit</a> <a href=\"map_editor.php?x=".$x."&y=".$y."&time=".$currentTime."&action=delete&delete_id=".$object['id']."\">del</a></td>\n".
             "          </tr>\n";
    }

    echo "        </table>\n";
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



?>
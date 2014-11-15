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
 * @file $/editor/detail_editor.php
 * @brief Simple detail editor.
 * @author Stephan Kreutzer
 * @since 2012-02-17
 */



$currentTime = date("H:i:s");

if (isset($_GET['time']) === true)
{
    require_once("editor.inc.php");

    if (checktime($_GET['time']) === true)
    {
        $currentTime = explode(":", $_GET['time'], 3);
        $currentTime = date("H:i:s", mktime($currentTime[0], $currentTime[1], $currentTime[2]));
    }
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
            db_set("DELETE FROM `detail_coords`\n".
                   "WHERE `detail_id`=".$_GET['delete_id']."\n");
            db_set("DELETE FROM `detail`\n".
                   "WHERE `id`=".$_GET['delete_id']."\n");

            if (isset($_GET['name']) === true)
            {
                $name = db_prepare_string($_GET['name']);

                if ($name != false)
                {
                    // Hard reset.

                    require_once(dirname(__FILE__)."/../data/globals.inc.php");

                    $image = imagecreatetruecolor(VIEW_X, VIEW_Y);
                    imagepng($image, dirname(__FILE__)."/../cache/".$_GET['name'].".png");
                    imagedestroy($image);

                    db_set("UPDATE `detail`\n".
                           "SET `visible`=NULL\n".
                           "WHERE `name` LIKE '".$name."\n");
                }
            }
        }
    }
}



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
     "      <title>Welt detail editor</title>\n".
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
     "        <form action=\"detail_editor.php\" method=\"get\">\n".
     "          <select name=\"name\" size=\"1\">\n";

$detail = db_get_rows("SELECT `name`\n".
                      "FROM `detail`\n".
                      "WHERE 1\n".
                      "GROUP BY `name`\n".
                      "ORDER BY `name` ASC\n");

if (is_array($detail) === true)
{
    foreach ($detail as $object)
    {
        echo "            <option value=\"".$object['name']."\"";

        if (isset($_GET['name']) === true)
        {
            if ($object['name'] == $_GET['name'])
            {
                echo " selected=\"selected\"";
            }
        }

        echo ">".$object['name']."</option>\n";
    }
}

echo "          </select> detail name<br />\n".
     "          <input name=\"time\" type=\"text\" size=\"8\" maxlength=\"8\" value=\"".$currentTime."\" /> time<br />\n".
     "          <input type=\"submit\" value=\"submit\" /><br />\n".
     "        </form>\n";

if (isset($_GET['name']) === true)
{
    require_once("../libraries/view_generator.inc.php");

    $success = generateDetail($_GET['name'], $currentTime);

    if ($success == 0)
    {
        echo "        <img src=\"../cache/".$_GET['name'].".png?dummy=".md5(uniqid(rand(), true))."\" border=\"1\" alt=\"Welt\" />\n";
    }

    $detail = db_get_rows("SELECT `detail`.`id`,\n".
                          "    `objects`.`file`,\n".
                          "    `detail`.`position_x`,\n".
                          "    `detail`.`position_y`,\n".
                          "    `detail`.`order_z`,\n".
                          "    `detail`.`from`,\n".
                          "    `detail`.`to`,\n".
                          "    `detail`.`visible`,\n".
                          "    `detail`.`night`\n".
                          "FROM `detail` JOIN `objects`\n".
                          "ON `detail`.`object_id`=`objects`.`id`\n".
                          "WHERE `detail`.`name` LIKE '".$_GET['name']."'\n".
                          "ORDER BY `detail`.`from` ASC,\n".
                          "    `detail`.`order_z` DESC,\n".
                          "    `detail`.`visible` ASC\n");

    if (is_array($detail) === true)
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
             "            <th>night</th>\n".
             "            <th>action</th>\n".
             "          </tr>\n";

        foreach ($detail as $object)
        {
            echo "          <tr>\n".
                 "            <td style=\"white-space:nowrap;\">".$object['file']."</td>\n".
                 "            <td>".$object['position_x']."</td>\n".
                 "            <td>".$object['position_y']."</td>\n".
                 "            <td>".$object['order_z']."</td>\n".
                 "            <td>".$object['from']."</td>\n".
                 "            <td>".$object['to']."</td>\n".
                 "            <td>".$object['visible']."</td>\n".
                 "            <td>".$object['night']."</td>\n".
                 "            <td style=\"white-space:nowrap;\"><a href=\"detail_update.php?name=".$_GET['name']."&time=".$currentTime."&id=".$object['id']."\">edit</a> <a href=\"detail_editor.php?name=".$_GET['name']."&time=".$currentTime."&action=delete&delete_id=".$object['id']."\">del</a></td>\n".
                 "          </tr>\n";
        }

        echo "        </table>\n";
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



?>
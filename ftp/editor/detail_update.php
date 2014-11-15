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
 * @file $/editor/detail_update.php
 * @brief Modify a single object on a detail view.
 * @author Stephan Kreutzer
 * @since 2012-02-21
 */



if (isset($_GET['id']) !== true)
{
    echo "no detail id.<br />";
    exit(-1);
}

if (is_numeric($_GET['id']) !== true)
{
    echo "invalid detail id.<br />";
    exit(-1);
}



require_once(dirname(__FILE__)."/../libraries/database.inc.php");



if (isset($_GET['order_z']) === true &&
    isset($_GET['from']) === true &&
    isset($_GET['to']) === true &&
    isset($_GET['night']) === true)
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
    
    if ($_GET['night'] != 0 &&
        $_GET['night'] != 1)
    {
        echo "night effect with invalid mode.<br />\n";
        $validData = false;
    }

    if ($validData === true)
    {
        db_set("UPDATE `detail`\n".
               "SET `order_z`=".$_GET['order_z'].",\n".
               "    `from`='".$timeFrom."',\n".
               "    `to`='".$timeTo."',\n".
               "    `visible`=NULL,\n".
               "    `night`=".$_GET['night']."\n".
               "WHERE `id`=".$_GET['id']."\n");
    }
    else
    {
        exit(-2);
    }
}

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
                   "WHERE `id`=".$_GET['delete_id']."\n");

            if (isset($_GET['name']) === true)
            {
                $name = db_prepare_string($_GET['name']);

                if ($name != false)
                {
                    // Hard reset.

                    $fp = @fopen(dirname(__FILE__)."/../cache/".$_GET['name'].".html", "w");
                    @fclose($fp);
                }
            }

            db_set("UPDATE `detail_coords`\n".
                   "SET `visible`=NULL\n".
                   "WHERE `detail_id`=".$_GET['id']."\n");
        }
    }
}



$detail = db_get_rows("SELECT `detail`.`name`,\n".
                      "    `objects`.`file`,\n".
                      "    `detail`.`order_z`,\n".
                      "    `detail`.`from`,\n".
                      "    `detail`.`to`,\n".
                      "    `detail`.`night`\n".
                      "FROM `detail` JOIN `objects`\n".
                      "ON `detail`.`object_id`=`objects`.`id`\n".
                      "WHERE `detail`.`id`=".$_GET['id']."\n");

if (is_array($detail) === true)
{
    $detail = $detail[0];
}
else
{
    echo "can't get detail data.<br />";
    exit(-3);
}



$name = "";
$time = date("H:i:s");

if (isset($_GET['name']) === true)
{
    if ($_GET['name'] == $detail['name'])
    {
        $name = $detail['name'];
    }
    else
    {
        echo "given detail name and detail name of the detail data do not match.<br />";
        exit(-4);
    }
}
else
{
    echo "no detail name.<br />";
    exit(-4);
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
     "      <title>Welt detail update</title>\n".
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



{
    $filePath = "../resources/".$detail['file'];
    $size = @getimagesize($filePath);

    if ($size == false)
    {
        $image = imagecreatetruecolor(50, 50);
    }
    else
    {
        $image = imagecreatetruecolor($size[0], $size[1]);
    }

    // Default background color to make transparency visible.
    $background = imagecolorallocate($image, 255, 20, 147);
    imagefill($image, 0, 0, $background);

    $objectImage = @imagecreatefrompng($filePath);

    if ($objectImage == false || $size == false)
    {
        echo "image file not found.<br />";
    }
    else
    {
        imagesavealpha($objectImage, true);
        imagecopy($image, $objectImage, 0, 0, 0, 0, $size[0], $size[1]);
        imagedestroy($objectImage);
    }

    imagepng($image, "../cache/temp_editor.png");
    imagedestroy($image);
}

echo "        <div>\n".
     "          <img src=\"../cache/temp_editor.png?dummy=".md5(uniqid(rand(), true))."\" border=\"1\" alt=\"".$detail['file']."\" title=\"".$detail['file']."\" />\n".
     "        </div>\n".
     "        <form action=\"detail_update.php\" method=\"get\">\n".
     "          <input name=\"order_z\" type=\"text\" size=\"3\" maxlength=\"20\" value=\"".$detail['order_z']."\" /> z order<br />\n".
     "          <input name=\"from\" type=\"text\" size=\"8\" maxlength=\"8\" value=\"".$detail['from']."\" /> from (between 00:00:00 and 23:59:59)<br />\n".
     "          <input name=\"to\" type=\"text\" size=\"8\" maxlength=\"8\" value=\"".$detail['to']."\" /> to (between 00:00:00 and 24:00:00)<br />\n".
     "          <select name=\"night\" size=\"1\">\n";
     
if ($detail['night'] == 1)
{
    echo "            <option value=\"1\" selected=\"selected\">yes</option>\n";
}
else
{
    echo "            <option value=\"1\">yes</option>\n";
}

if ($detail['night'] == 0)
{
    echo "            <option value=\"0\" selected=\"selected\">no</option>\n";
}
else
{
    echo "            <option value=\"0\">no</option>\n";
}

echo "          </select> night effect<br />\n".
     "          <input name=\"id\" type=\"hidden\" value=\"".$_GET['id']."\" />\n".
     "          <input name=\"name\" type=\"hidden\" value=\"".$name."\" />\n".
     "          <input name=\"time\" type=\"hidden\" value=\"".$time."\" />\n".
     "          <input type=\"submit\" value=\"update\" /><br />\n".
     "        </form>\n";

$coords = db_get_rows("SELECT `id`,\n".
                        "    `order_z`,\n".
                        "    `from`,\n".
                        "    `to`,\n".
                        "    `visible`,\n".
                        "    `href`,\n".
                        "    `alt`,\n".
                        "    `title`\n".
                        "FROM `detail_coords`\n".
                        "WHERE `detail_id`=".$_GET['id']."\n".
                        "ORDER BY `from` ASC,\n".
                        "    `order_z` DESC,\n".
                        "    `visible` ASC\n");

if (is_array($coords) === true)
{
    echo "        <table border=\"1\">\n".
            "          <tr>\n".
            "            <th style=\"white-space:nowrap;\">z order</th>\n".
            "            <th>from</th>\n".
            "            <th>to</th>\n".
            "            <th>visible</th>\n".
            "            <th>href</th>\n".
            "            <th>alt</th>\n".
            "            <th>title</th>\n".
            "            <th>action</th>\n".
            "          </tr>\n";

    foreach ($coords as $coord)
    {
        echo "          <tr>\n".
                "            <td>".$coord['order_z']."</td>\n".
                "            <td>".$coord['from']."</td>\n".
                "            <td>".$coord['to']."</td>\n".
                "            <td>".$coord['visible']."</td>\n".
                "            <td style=\"white-space:nowrap;\"><a href=\"../".$coord['href']."\">".$coord['href']."</a></td>\n".
                "            <td style=\"white-space:nowrap;\">".$coord['alt']."</td>\n".
                "            <td style=\"white-space:nowrap;\">".$coord['title']."</td>\n".
                "            <td style=\"white-space:nowrap;\"><a href=\"detail_coord_update.php?name=".$_GET['name']."&time=".$time."&id=".$_GET['id']."&coord_id=".$coord['id']."\">edit</a> <a href=\"detail_update.php?name=".$_GET['name']."&time=".$time."&id=".$_GET['id']."&action=delete&delete_id=".$coord['id']."\">del</a></td>\n".
                "          </tr>\n";
    }

    echo "        </table>\n";
}

echo "        <ul>\n".
     "          <li>\n".
     "            <a href=\"detail_coord_insert.php?name=".$_GET['name']."&time=".$time."&id=".$_GET['id']."\">detail coord insert</a>\n".
     "          </li>\n".
     "        </ul>\n".
     "        <div>\n".
     "          <a href=\"detail_editor.php?name=".$_GET['name']."&time=".$time."\">detail editor</a>\n".
     "        </div>\n".
     "      </div>\n".
     "\n".
     "\n".
     "  </body>\n".
     "\n".
     "\n".
     "</html>\n";



?>
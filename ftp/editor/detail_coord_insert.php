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
 * @file $/editor/detail_coord_insert.php
 * @brief Insert a clickable area into the detail view.
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



$detail = db_get_rows("SELECT `name`\n".
                      "FROM `detail`\n".
                      "WHERE `id`=".$_GET['id']."\n");

if (is_array($detail) === true)
{
    $detail = $detail[0];
}
else
{
    echo "can't get detail data.<br />";
    exit(-2);
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
        exit(-3);
    }
}
else
{
    echo "no detail name.<br />";
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
     "      <title>Welt detail coord insert</title>\n".
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

if (isset($_GET['order_z']) != true ||
    isset($_GET['from']) != true ||
    isset($_GET['to']) != true ||
    isset($_GET['coords']) != true ||
    isset($_GET['href']) != true ||
    isset($_GET['alt']) != true ||
    isset($_GET['title']) != true)
{
    echo "        <form action=\"detail_coord_insert.php\" method=\"get\">\n".
         "          <input name=\"order_z\" type=\"text\" size=\"3\" maxlength=\"20\" /> z order<br />\n".
         "          <input name=\"from\" type=\"text\" size=\"8\" maxlength=\"8\" /> from (between 00:00:00 and 23:59:59)<br />\n".
         "          <input name=\"to\" type=\"text\" size=\"8\" maxlength=\"8\" /> to (between 00:00:00 and 24:00:00)<br />\n".
         "          <input name=\"coords\" type\"text\" size=\"80\" /> coords (x and y coordinate pairs separated by comma)<br />\n".
         "          <input name=\"href\" type=\"text\" size=\"80\" maxlength=\"255\" /> link<br />\n".
         "          <input name=\"alt\" type=\"text\" size=\"80\" maxlength=\"255\" /> alternative text<br />\n".
         "          <input name=\"title\" type=\"text\" size=\"80\" maxlength=\"255\" /> title<br />\n".
         "          <input name=\"id\" type=\"hidden\" value=\"".$_GET['id']."\" />\n".
         "          <input name=\"name\" type=\"hidden\" value=\"".$name."\" />\n".
         "          <input name=\"time\" type=\"hidden\" value=\"".$time."\" />\n".
         "          <input type=\"submit\" value=\"insert\" /><br />\n".
         "        </form>\n";
}
else
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
        $id = db_insert("INSERT INTO `detail_coords` (`id`,\n".
                        "    `name`,\n".
                        "    `detail_id`,\n".
                        "    `order_z`,\n".
                        "    `from`,\n".
                        "    `to`,\n".
                        "    `visible`,\n".
                        "    `coords`,\n".
                        "    `href`,\n".
                        "    `alt`,\n".
                        "    `title`)\n".
                        "VALUES (NULL,\n".
                        "    '".$name."',\n".
                        "    ".$_GET['id'].",\n".
                        "    ".$_GET['order_z'].",\n".
                        "    '".$timeFrom."',\n".
                        "    '".$timeTo."',\n".
                        "    0,\n".
                        "    '".$_GET['coords']."',\n".
                        "    '".$_GET['href']."',\n".
                        "    '".$_GET['alt']."',\n".
                        "    '".$_GET['title']."')");

        echo "regular id: ".$id."<br />";
    }
}

echo "        <div>\n".
     "          <a href=\"detail_update.php?name=".$name."&time=".$time."&id=".$_GET['id']."\">detail update</a>\n".
     "        </div>\n".
     "      </div>\n".
     "\n".
     "\n".
     "  </body>\n".
     "\n".
     "\n".
     "</html>\n";



?>
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
 * @file $/editor/object_insert.php
 * @brief Insert a new object that may be used in the map or
 *     detail view.
 * @author Stephan Kreutzer
 * @since 2012-02-17
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
     "      <title>Welt object insert</title>\n".
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

if (isset($_GET['file']) != true)
{
    echo "        <form action=\"object_insert.php\" method=\"get\">\n".
         "          <input name=\"file\" type=\"text\" size=\"80\" maxlength=\"255\" /> file (*.png in $/resources/)<br />\n".
         "          <input type=\"submit\" value=\"insert\" /><br />\n".
         "        </form>\n";
}
else
{
    $validFile = true;

    $file = $_GET['file'];
    $filePath = dirname(__FILE__)."/../resources/".$file;
    $size = @getimagesize($filePath);

    if ($size == false)
    {
        echo "file not found.<br />";
        $validFile = false;
    }
    
    if ($validFile === true)
    {
        if ($size[2] != IMAGETYPE_PNG)
        {
            echo "isn't *.png file.<br />";
            $validFile = false;
        }
    }
    
    if ($validFile === true)
    {
        if ($size[0] > VIEW_X ||
            $size[0] <= 0 ||
            $size[1] > VIEW_Y ||
            $size[1] <= 0)
        {
            echo "bad size.<br />";
            $validFile = false;
        }
    }

    if ($validFile === true)
    {
        $file = db_prepare_string($file);
        
        if ($file == false)
        {
            echo "file string invalid.<br />";
            $validFile = false;
        }
    }
    
    if ($validFile === true)
    {
        $id = db_insert("INSERT INTO `objects` (`id`,\n".
                        "    `file`)\n".
                        "VALUES (NULL,\n".
                        "    '".$file."')");

        if (is_numeric($id) === true)
        {
            echo "regular id: ".$id."<br />";
        }
        else
        {
            echo "insertion failed.<br />";
        }
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
<?php
/* Copyright (C) 2011  Stephan Kreutzer
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
 * @file $/detail.php
 * @brief Web page that hosts a detail view (used for inner rooms,
 *     close-ups etc.).
 * @author Stephan Kreutzer
 * @since 2011-09-28
 */



if (isset($_GET['name']) !== true)
{
    exit(-1);
}



require_once("libraries/view_generator.inc.php");

$currentTime = date("H:i:s");
$success = generateDetail($_GET['name'], $currentTime);

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
     "      <title>Welt</title>\n".
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

if ($success == 0)
{
    echo "        <img src=\"cache/".$_GET['name'].".png?dummy=".md5(uniqid(rand(), true))."\" border=\"1\" alt=\"Welt\" usemap=\"#default\"/>\n".
         "        <map name=\"default\">\n";

    $mapAreas = @file_get_contents(dirname(__FILE__)."/cache/".$_GET['name'].".html");

    if ($mapAreas != false)
    {
        echo $mapAreas;
    }

    echo "        </map>\n";
}

echo "        <p>\n".
     "          Welt um ".$currentTime.".\n".
     "        </p>\n".
     "      </div>\n".
     "\n".
     "\n".
     "  </body>\n".
     "\n".
     "\n".
     "</html>\n";



?>
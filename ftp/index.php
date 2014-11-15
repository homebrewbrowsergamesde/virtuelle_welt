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
 * @file $/index.php
 * @brief Web page that hosts the global map view.
 * @author Stephan Kreutzer
 * @since 2011-09-28
 */



$x = 0;
$y = 0;

if (isset($_GET['x']) === true)
{
    $x = $_GET['x'];
}

if (isset($_GET['y']) === true)
{
    $y = $_GET['y'];
}

if (is_numeric($x) !== true ||
    is_numeric($y) !== true)
{
    exit(-1);
}



require_once("libraries/view_generator.inc.php");

$currentTime = date("H:i:s");
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
     "      <div>\n".
     "        <p>\n".
     "          <a href=\"index.php?x=".($x - 1)."&y=".$y."\">Links</a>,\n".
     "          <a href=\"index.php?x=".$x."&y=".($y - 1)."\">oben</a>,\n".
     "          <a href=\"index.php?x=".($x + 1)."&y=".$y."\">rechts</a> oder\n".
     "          <a href=\"index.php?x=".$x."&y=".($y + 1)."\">unten</a> entlang.\n".
     "        </p>\n";

if ($success == 0)
{
    echo "        <img src=\"cache/".$x."_".$y.".png?dummy=".md5(uniqid(rand(), true))."\" border=\"1\" alt=\"Welt[".$x.",".$y."]\" usemap=\"#default\" />\n".
         "        <map name=\"default\">\n";

    $mapAreas = @file_get_contents(dirname(__FILE__)."/cache/".$x."_".$y.".html");

    if ($mapAreas != false)
    {
        echo $mapAreas;
    }

    echo "        </map>\n";
}

echo "        <p>\n".
     "          Welt[".$x.",".$y."] um ".$currentTime.".\n".
     "        </p>\n".
     "      </div>\n".
     "\n".
     "\n".
     "  </body>\n".
     "\n".
     "\n".
     "</html>\n";



?>
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
 * @file $/editor/visible_reset.php
 * @brief Resets globally all information about which objects are
 *     currently visible, so the software is in a state like directly
 *     after setup.
 * @author Stephan Kreutzer
 * @since 2012-03-09
 */



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
     "      <title>Welt global visible reset</title>\n".
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
     "        <p>\n";

$success = true;

if (db_set("UPDATE `map`\n".
           "SET `visible`=NULL\n".
           "WHERE 1\n") !== true)
{
    echo "          failed to reset map.<br />\n";
    $success = false;
}

if (db_set("UPDATE `map_coords`\n".
           "SET `visible`=0\n".
           "WHERE 1\n") !== true)
{
    echo "          failed to reset map coords.<br />\n";
    $success = false;
}

if (db_set("UPDATE `detail`\n".
           "SET `visible`=NULL\n".
           "WHERE 1\n") !== true)
{
    echo "          failed to reset detail view.<br />\n";
    $success = false;
}

if (db_set("UPDATE `detail_coords`\n".
           "SET `visible`=0\n".
           "WHERE 1\n") !== true)
{
       echo "          failed to reset detail view coords.<br />\n";
    $success = false;
}

if ($success === true)
{
    echo "          done.\n";
}

echo "        </p>\n".
     "        <div>\n".
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
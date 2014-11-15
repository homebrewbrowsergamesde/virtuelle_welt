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
 * @file $/editor/index.php
 * @brief Menu of the Welt editor.
 * @author Stephan Kreutzer
 * @since 2012-02-16
 */



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
     "      <title>Welt editor</title>\n".
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
     "      <p>\n".
     "        Always make a dump (backup) of your database before using\n".
     "        the editor!\n".
     "      </p>\n".
     "      <ul>\n".
     "        <li>\n".
     "          <a href=\"object_insert.php\">object insert</a>\n".
     "        </li>\n".
     "        <li>\n".
     "          <a href=\"map_editor.php\">map editor</a>\n".
     "        </li>\n".
     "        <li>\n".
     "          <a href=\"map_insert.php\">map insert</a>\n".
     "        </li>\n".
     "        <li>\n".
     "          <a href=\"detail_editor.php\">detail editor</a>\n".
     "        </li>\n".
     "        <li>\n".
     "          <a href=\"detail_insert.php\">detail insert</a>\n".
     "        </li>\n".
     "        <li>\n".
     "          <a href=\"visible_reset.php\">global visible reset</a>\n".
     "        </li>\n".
     "      </ul>\n".
     "\n".
     "\n".
     "  </body>\n".
     "\n".
     "\n".
     "</html>\n";



?>
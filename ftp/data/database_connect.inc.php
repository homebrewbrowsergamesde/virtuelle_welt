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
 * @file $/data/database_connect.inc.php
 * @brief Verbindet automatisch zur zuständigen Datenbank mit dem fest
 *     codierten Benutzernamen und Passwort.
 * @details Nach dem Einbinden wird die globale <tt>$mysql_connection</tt>-Variable
 *     ein gültiges MySQL-Verbindungs-Handle (oder <tt>false</tt> bei Misserfolg)
 *     enthalten.
 * @author Stephan Kreutzer
 * @since 2011-09-28
 */



$mysql_connection = @mysql_connect("localhost", "weltuser", "password");

if ($mysql_connection != false)
{
    if (@mysql_query("USE welt", $mysql_connection) == false)
    {
        @mysql_close($mysql_connection);
        $mysql_connection = false;
    }
}

global $mysql_connection;



?>
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
 * @file $/libraries/database.inc.php
 * @brief Provides basic database functionality.
 * @author Stephan Kreutzer
 * @since 2011-09-28
 */



// Will automatically connect to the database and will provide
// the global variable $mysql_connection, containing either
// a valid MySQL connection handle or 'false'.
require_once(dirname(__FILE__)."/../data/database_connect.inc.php");



/**
 * @brief Will prepare \p $string for secure usage in a SQL
 *     query.
 * @return The prepared string.
 * @retval false Unable to prepare \p $string for secure usage
 *     in a SQL query.
 */
function db_prepare_string($string)
{
    if (is_string($string) !== true)
    {
        return true;
    }

    $string = @mysql_real_escape_string($string);

    if (is_string($string) !== true)
    {
        return false;
    }

    return $string;
}

/**
 * @brief Will start a MySQL transaction.
 * @retval true Transaction started.
 */
function db_transaction_begin()
{
    global $mysql_connection;

    if ($mysql_connection == false)
        return NULL;

    if (@mysql_query("BEGIN") === true)
        return true;

    return false;
}

/**
 * @brief Commit a MySQL transaction started with db_transaction_begin().
 * @retval true Transaction commited.
 */
function db_transaction_commit()
{
    global $mysql_connection;

    if ($mysql_connection == false)
        return NULL;

    if (@mysql_query("COMMIT") === true)
        return true;

    return false;
}

/**
 * @brief Discard a MySQL transaction started with db_transaction_begin().
 * @retval true Transaction discarded.
 */
function db_transaction_discard()
{
    global $mysql_connection;

    if ($mysql_connection == false)
        return NULL;

    if (@mysql_query("ROLLBACK") === true)
        return true;

    return false;
}

/**
 * @brief Executes <tt>UPDATE</tt> SQL queries.
 * @param[in] $sql_string Should be a SQL <tt>UPDATE</tt> statement.
 * @retval NULL Query failed.
 * @retval true Success.
 */
function db_set($sql_string)
{
    global $mysql_connection;

    if ($mysql_connection == false)
        return NULL;

    $result = @mysql_query($sql_string, $mysql_connection);

    if ($result === true)
        return true;

    return NULL;
}

/**
 * @brief Optimized to execute <tt>INSERT</tt> SQL queries.
 * @param[in] $sql_string Should be a SQL <tt>INSERT</tt> statement.
 * @return <tt>AUTO_INCREMENT</tt> ID of the inserted entry.
 * @retval NULL Query failed.
 * @retval false No <tt>AUTO_INCREMENT</tt> ID retrieved, but the
 *     statement itself was executed successfully.
 */
function db_insert($sql_string)
{
    global $mysql_connection;

    if ($mysql_connection == false)
        return NULL;

    $result = @mysql_query($sql_string, $mysql_connection);

    if ($result === true)
    {
        $result = @mysql_insert_id($mysql_connection);

        if (is_numeric($result) === true)
        {
            if ($result > 0)
            {
                return $result;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    return NULL;
}

/**
 * @brief Executes <tt>SELECT</tt> SQL queries and provides the result
 *     as an array.
 * @param[in] $sql_string Should be a SQL <tt>SELECT</tt> statement.
 * @param[in] $associative=true Specifies if the resulting array should be
 *     associative. If <tt>true</tt>, elements will be named after the
 *     original table names or the names given by the <tt>AS</tt> statement.
 * @return Two-dimensional array with the first dimension for each result
 *     entry and the second for the single values.
 * @retval NULL Query failed.
 * @retval false No result entries.
 */
function db_get_rows($sql_string, $associative = true)
{
    global $mysql_connection;

    if ($mysql_connection == false)
        return NULL;

    $result_rows = NULL;

    $result = @mysql_query($sql_string, $mysql_connection);

    if ($result !== false)
    {
        if ($result === true)
            return false;

        if ((@mysql_num_rows($result) > 0) == false)
            return false;


        if ($associative == true)
        {
            $associative = MYSQL_ASSOC;
        }
        else
        {
            $associative = MYSQL_NUM;
        }


        while (1)
        {
            if (($row = @mysql_fetch_array($result, $associative)) != false)
            {
                $result_rows[] = $row;
            }
            else
            {
                break;
            }
        }

        @mysql_free_result($result);

        if (is_array($result_rows) !== true)
            return NULL;

        if (count($result_rows) <= 0)
            return false;

        return $result_rows;
    }
    else
    {
        return NULL;
    }
}



?>
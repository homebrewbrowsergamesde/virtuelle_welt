<?php
/* Copyright (C) 2011-2012  Stephan Kreutzer
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
 * @file $/libraries/view_generator.inc.php
 * @brief Provides functions to generate the image map for the GUI.
 * @author Stephan Kreutzer
 * @since 2011-09-28
 */



require_once(dirname(__FILE__)."/../data/globals.inc.php");
require_once(dirname(__FILE__)."/database.inc.php");



function generateMap($x, $y, $currentTime)
{
    if (is_numeric($x) !== true ||
        is_numeric($y) !== true)
    {
        return -1;
    }

    $x = db_prepare_string((string)$x);
    $y = db_prepare_string((string)$y);
    // Expected to do no changes - required for string
    // comparisons.
    $currentTime = db_prepare_string($currentTime);

    if ($x === false ||
        $y === false ||
        $currentTime === false)
    {
        return -1;
    }

    $map = db_get_rows("SELECT `map`.`id`,\n".
                       "    `objects`.`file`,\n".
                       "    `map`.`position_x`,\n".
                       "    `map`.`position_y`,\n".
                       "    `map`.`from`,\n".
                       "    `map`.`to`,\n".
                       "    `map`.`visible`\n".
                       "FROM `map` JOIN `objects`\n".
                       "ON `map`.`object_id`=`objects`.`id`\n".
                       "WHERE `map`.`map_x`=".$x." AND\n".
                       "    `map`.`map_y`=".$y."\n".
                       "ORDER BY `map`.`order_z` ASC,\n".
                       "    `map`.`position_y` ASC,\n".
                       "    `map`.`position_x` ASC");

    if (is_array($map) !== true)
    {
        return -2;
    }

    $newObjects = false;
    $oldObjects = false;

    foreach ($map as &$object)
    {
        // No day overflow between 'from' and 'to'?
        if ($object['from'] <= $object['to'])
        {
            // Current time between 'from' and 'to'?
            if ($object['from'] <= $currentTime &&
                $object['to'] > $currentTime)
            {
                if ($object['visible'] === NULL)
                {
                    $newObjects = true;
                    // Will become visible.
                    $object['visible'] = $currentTime;
                }
                else
                {
                    // Is it day?
                    if (DAY_FROM <= $currentTime &&
                        DAY_TO > $currentTime)
                    {
                        // Is the object visible in its nightly shape?
                        if ($object['visible'] < DAY_FROM ||
                            $object['visible'] >= DAY_TO)
                        {
                            // Shift happened since the last appliance of the
                            // object, so generally redraw the object.
                            $newObjects = true;
                            $object['visible'] = $currentTime;
                        }
                    }
                    else
                    {
                        // Is the object visible in its daily shape?
                        if ($object['visible'] >= DAY_FROM &&
                            $object['visible'] < DAY_TO)
                        {
                            // Shift happened since the last appliance of the
                            // object, so generally redraw the object.
                            $newObjects = true;
                            $object['visible'] = $currentTime;
                        }
                    }
                }
            }
            else
            {
                if ($object['visible'] !== NULL)
                {
                    $oldObjects = true;
                    // Will become invisible.
                    $object['visible'] = NULL;
                }
            }
        }
        else
        {
            // Current time between 'from' and 'to'?
            if ($object['from'] <= $currentTime ||
                $object['to'] > $currentTime)
            {
                if ($object['visible'] === NULL)
                {
                    $newObjects = true;
                    // Will become visible.
                    $object['visible'] = $currentTime;
                }
                else
                {
                    // Is it day?
                    if (DAY_FROM <= $currentTime &&
                        DAY_TO > $currentTime)
                    {
                        // Is the object visible in its nightly shape?
                        if ($object['visible'] < DAY_FROM ||
                            $object['visible'] >= DAY_TO)
                        {
                            // Shift happened since the last appliance of the
                            // object, so generally redraw the object.
                            $newObjects = true;
                            $object['visible'] = $currentTime;
                        }
                    }
                    else
                    {
                        // Is the object visible in its daily shape?
                        if ($object['visible'] >= DAY_FROM &&
                            $object['visible'] < DAY_TO)
                        {
                            // Shift happened since the last appliance of the
                            // object, so generally redraw the object.
                            $newObjects = true;
                            $object['visible'] = $currentTime;
                        }
                    }
                }
            }
            else
            {
                if ($object['visible'] !== NULL)
                {
                    $oldObjects = true;
                    // Will become invisible.
                    $object['visible'] = NULL;
                }
            }
        }
    }

    // Needed because of referenced foreach loop.
    unset($object);

    if ($newObjects === true)
    {
        db_set("UPDATE `map`\n".
               "SET `visible`='".$currentTime."'\n".
               "WHERE `map_x`=".$x." AND\n".
               "    `map_y`=".$y." AND\n".
               "    `from`<=`to` AND\n".
               "    (`from`<='".$currentTime."' AND\n".
               "    `to`>'".$currentTime."')");

        // Day overflow.
        db_set("UPDATE `map`\n".
               "SET `visible`='".$currentTime."'\n".
               "WHERE `map_x`=".$x." AND\n".
               "    `map_y`=".$y." AND\n".
               "    `from`>`to` AND\n".
               "    (`from`<='".$currentTime."' OR\n".
               "    `to`>'".$currentTime."')");
    }

    if ($oldObjects === true)
    {
        db_set("UPDATE `map`\n".
               "SET `visible`=NULL\n".
               "WHERE `map_x`=".$x." AND\n".
               "    `map_y`=".$y." AND\n".
               "    `from`<=`to` AND\n".
               "    (`from`>'".$currentTime."' OR\n".
               "    `to`<='".$currentTime."')");

        // Day overflow.
        db_set("UPDATE `map`\n".
               "SET `visible`=NULL\n".
               "WHERE `map_x`=".$x." AND\n".
               "    `map_y`=".$y." AND\n".
               "    `from`>`to` AND\n".
               "    (`from`>'".$currentTime."' AND\n".
               "    `to`<='".$currentTime."')");
    }

    if ($newObjects === true ||
        $oldObjects === true)
    {
        $image = imagecreatetruecolor(VIEW_X, VIEW_Y);
        $objectCount = 0;

        foreach ($map as $object)
        {
            if ($object['visible'] === NULL)
            {
                continue;
            }
            else
            {
                $objectCount++;
            }

            $filePath = dirname(__FILE__)."/../resources/".$object['file'];
            $size = getimagesize($filePath);

            $objectImage = imagecreatefrompng($filePath);
            imagesavealpha($objectImage, true);
            // -1 for $size[1] if used in positioning context because
            // the image size is not zero-based but the coordinate system is.
            imagecopy($image, $objectImage, $object['position_x'], ($object['position_y'] - ($size[1] - 1)), 0, 0, $size[0], $size[1]);
            imagedestroy($objectImage);
        }

        // Apply night effect?
        if ($objectCount > 0 &&
            ($currentTime < DAY_FROM ||
             $currentTime >= DAY_TO))
        {
            if (function_exists('imagefilter') === true)
            {
                //imagefilter($image, IMG_FILTER_GRAYSCALE);
                imagefilter($image, IMG_FILTER_NEGATE);
            }
            else
            {
                for ($negate_x = 0; $negate_x < VIEW_X; $negate_x++)
                {
                    for ($negate_y = 0; $negate_y < VIEW_Y; $negate_y++)
                    {
                        $negate_color_pixel = imagecolorat($image, $negate_x, $negate_y);
                        $negate_rgb_pixel = imagecolorsforindex($image, $negate_color_pixel);

                        $negated_pixel = imagecolorallocate($image,
                                                            255 - $negate_rgb_pixel['red'],
                                                            255 - $negate_rgb_pixel['green'],
                                                            255 - $negate_rgb_pixel['blue']);

                        imagesetpixel($image, $negate_x, $negate_y, $negated_pixel);
                    }
                }
            }
        }

        imagepng($image, dirname(__FILE__)."/../cache/".$x."_".$y.".png");
        imagedestroy($image);
    }


    $coords = db_get_rows("SELECT `map_id`,\n".
                          "    `from`,\n".
                          "    `to`,\n".
                          "    `visible`,\n".
                          "    `coords`,\n".
                          "    `href`,\n".
                          "    `alt`,\n".
                          "    `title`\n".
                          "FROM `map_coords`\n".
                          "WHERE `map_x`=".$x." AND\n".
                          "    `map_y`=".$y."\n".
                          // The first image map area in HTML wins, therefore z-order DESC.
                          "ORDER BY `order_z` DESC\n");

    if (is_array($coords) !== true)
    {
        return 0;
    }

    $newCoords = false;
    $oldCoords = false;

    foreach ($coords as &$coord)
    {
        // No day overflow between 'from' and 'to'?
        if ($coord['from'] <= $coord['to'])
        {
            // Current time between 'from' and 'to'?
            if ($coord['from'] <= $currentTime &&
                $coord['to'] > $currentTime)
            {
                if ($coord['visible'] == "0")
                {
                    $newCoords = true;
                    // Will become accessible.
                    $coord['visible'] = "1";
                }
            }
            else
            {
                if ($coord['visible'] == "1")
                {
                    $oldCoords = true;
                    // Will become inaccessible.
                    $coord['visible'] = "0";
                }
            }
        }
        else
        {
            // Current time between 'from' and 'to'?
            if ($coord['from'] <= $currentTime ||
                $coord['to'] > $currentTime)
            {
                if ($coord['visible'] == "0")
                {
                    $newCoords = true;
                    // Will become accessible.
                    $coord['visible'] = "1";
                }
            }
            else
            {
                if ($coord['visible'] == "1")
                {
                    $oldCoords = true;
                    // Will become inaccessible.
                    $coord['visible'] = "0";
                }
            }
        }
    }

    // Needed because of referenced foreach loop.
    unset($coord);

    if ($newCoords === true)
    {
        db_set("UPDATE `map_coords`\n".
               "SET `visible`=1\n".
               "WHERE `map_x`=".$x." AND\n".
               "    `map_y`=".$y." AND\n".
               "    `from`<=`to` AND\n".
               "    (`from`<='".$currentTime."' AND\n".
               "    `to`>'".$currentTime."')");

        // Day overflow.
        db_set("UPDATE `map_coords`\n".
               "SET `visible`=1\n".
               "WHERE `map_x`=".$x." AND\n".
               "    `map_y`=".$y." AND\n".
               "    `from`>`to` AND\n".
               "    (`from`<='".$currentTime."' OR\n".
               "    `to`>'".$currentTime."')");
    }

    if ($oldCoords === true)
    {
        db_set("UPDATE `map_coords`\n".
               "SET `visible`=0\n".
               "WHERE `map_x`=".$x." AND\n".
               "    `map_y`=".$y." AND\n".
               "    `from`<=`to` AND\n".
               "    (`from`>'".$currentTime."' OR\n".
               "    `to`<='".$currentTime."')");

        // Day overflow.
        db_set("UPDATE `map_coords`\n".
               "SET `visible`=0\n".
               "WHERE `map_x`=".$x." AND\n".
               "    `map_y`=".$y." AND\n".
               "    `from`>`to` AND\n".
               "    (`from`>'".$currentTime."' AND\n".
               "    `to`<='".$currentTime."')");
    }

    if ($newCoords === true ||
        $oldCoords === true)
    {
        $mapAreas = "";

        foreach ($coords as &$coord)
        {
            if ($coord['visible'] == "0")
            {
                continue;
            }

            $objectFound = false;

            foreach ($map as $object)
            {
                if ($object['visible'] === NULL)
                {
                    continue;
                }

                if ($object['id'] === $coord['map_id'])
                {
                    $coord['position_x'] = $object['position_x'];
                    $coord['position_y'] = $object['position_y'];
                    $coord['file'] = $object['file'];
                    $objectFound = true;
                    break;
                }
            }

            if ($objectFound !== true)
            {
                // Data inconsistency.
                return -3;
            }

            $coord_xy = explode(",", $coord['coords']);
            $coord_count = count($coord_xy);

            if ($coord_count % 2 != 0)
            {
                continue;
            }

            // Maybe keep size from image processing above to optimize performance.
            $filePath = dirname(__FILE__)."/../resources/".$coord['file'];
            $size = getimagesize($filePath);

            $mapAreas .= "<area shape=\"poly\" coords=\"";

            for ($i = 0; $i < $coord_count; $i += 2)
            {
                $new_x = $coord_xy[$i] + $coord['position_x'];
                // -1 for $size[1] if used in positioning context because
                // the image size is not zero-based but the coordinate system is.
                $new_y = ($coord['position_y'] - ($size[1] - 1)) + $coord_xy[$i+1];

                if ($new_x < 0)
                    $new_x = 0;

                if ($new_x >= VIEW_X)
                    // -1 for VIEW_X if used in positioning context because
                    // the view size is not zero-based but the coordinate system is.
                    $new_x = VIEW_X - 1;

                if ($new_y < 0)
                    $new_y = 0;

                if ($new_y >= VIEW_Y)
                    // -1 for VIEW_Y if used in positioning context because
                    // the view size is not zero-based but the coordinate system is.
                    $new_y = VIEW_Y - 1;

                $mapAreas .= $new_x.",".$new_y.",";
            }

            // Remove trailing ",".
            $mapAreas = substr($mapAreas, 0, -1);

            $mapAreas .= "\" href=\"".$coord['href']."\" alt=\"".$coord['alt']."\" title=\"".$coord['title']."\" />\n";
        }

        // Needed because of referenced foreach loop.
        unset($coord);

        $fp = @fopen(dirname(__FILE__)."/../cache/".$x."_".$y.".html", "w");

        if ($fp != false)
        {
            if (@fwrite($fp, $mapAreas) == false)
            {
                if (strlen($mapAreas) > 0)
                {
                    return -4;
                }
            }

            @fclose($fp);
        }
    }

    return 0;
}

function generateDetail($name, $currentTime)
{
    if (is_string($name) !== true)
    {
        return -1;
    }

    $name = db_prepare_string($name);
    // Expected to do no changes - required for string
    // comparisons.
    $currentTime = db_prepare_string($currentTime);

    if ($name === false ||
        $currentTime === false)
    {
        return -1;
    }

    $detail = db_get_rows("SELECT `detail`.`id`,\n".
                          "    `objects`.`file`,\n".
                          "    `detail`.`position_x`,\n".
                          "    `detail`.`position_y`,\n".
                          "    `detail`.`from`,\n".
                          "    `detail`.`to`,\n".
                          "    `detail`.`visible`,\n".
                          "    `detail`.`night`\n".
                          "FROM `detail` JOIN `objects`\n".
                          "ON `detail`.`object_id`=`objects`.`id`\n".
                          "WHERE `detail`.`name` LIKE '".$name."'\n".
                          "ORDER BY `detail`.`order_z` ASC,\n".
                          "    `detail`.`position_y` ASC,\n".
                          "    `detail`.`position_x` ASC");

    if (is_array($detail) !== true)
    {
        return -2;
    }

    $newObjects = false;
    $oldObjects = false;
    $applyNight = false;

    foreach ($detail as &$object)
    {
        if ($object['night'] == "1")
        {
            $applyNight = true;        
        }

        // No day overflow between 'from' and 'to'?
        if ($object['from'] <= $object['to'])
        {
            // Current time between 'from' and 'to'?
            if ($object['from'] <= $currentTime &&
                $object['to'] > $currentTime)
            {
                if ($object['visible'] === NULL)
                {
                    $newObjects = true;
                    // Will become visible.
                    $object['visible'] = $currentTime;
                }
                else
                {
                    // Is the night effect enabled so that the current
                    // object is allowed to force a redraw?
                    if ($object['night'] == "1")
                    {
                        // Is it day?
                        if (DAY_FROM <= $currentTime &&
                            DAY_TO > $currentTime)
                        {
                            // Is the object visible in its nightly shape?
                            if ($object['visible'] < DAY_FROM ||
                                $object['visible'] >= DAY_TO)
                            {
                                // Shift happened since the last appliance of the
                                // object, so generally redraw the object.
                                $newObjects = true;
                                $object['visible'] = $currentTime;
                            }
                        }
                        else
                        {
                            // Is the object visible in its daily shape?
                            if ($object['visible'] >= DAY_FROM &&
                                $object['visible'] < DAY_TO)
                            {
                                // Shift happened since the last appliance of the
                                // object, so generally redraw the object.
                                $newObjects = true;
                                $object['visible'] = $currentTime;
                            }
                        }
                    }
                }
            }
            else
            {
                if ($object['visible'] !== NULL)
                {
                    $oldObjects = true;
                    // Will become invisible.
                    $object['visible'] = NULL;
                }
            }
        }
        else
        {
            if ($object['from'] <= $currentTime ||
                $object['to'] > $currentTime)
            {
                if ($object['visible'] === NULL)
                {
                    $newObjects = true;
                    // Will become visible.
                    $object['visible'] = $currentTime;
                }
                else
                {
                    // Is the night effect enabled so that the current
                    // object is allowed to force a redraw?
                    if ($object['night'] == "1")
                    {
                        // Is it day?
                        if (DAY_FROM <= $currentTime &&
                            DAY_TO > $currentTime)
                        {
                            // Is the object visible in its nightly shape?
                            if ($object['visible'] < DAY_FROM ||
                                $object['visible'] >= DAY_TO)
                            {
                                // Shift happened since the last appliance of the
                                // object, so generally redraw the object.
                                $newObjects = true;
                                $object['visible'] = $currentTime;
                            }
                        }
                        else
                        {
                            // Is the object visible in its daily shape?
                            if ($object['visible'] >= DAY_FROM &&
                                $object['visible'] < DAY_TO)
                            {
                                // Shift happened since the last appliance of the
                                // object, so generally redraw the object.
                                $newObjects = true;
                                $object['visible'] = $currentTime;
                            }
                        }
                    }
                }
            }
            else
            {
                if ($object['visible'] !== NULL)
                {
                    $oldObjects = true;
                    // Will become invisible.
                    $object['visible'] = NULL;
                }
            }
        }
    }

    // Needed because of referenced foreach loop.
    unset($object);

    if ($newObjects === true)
    {
        db_set("UPDATE `detail`\n".
               "SET `visible`='".$currentTime."'\n".
               "WHERE `name` LIKE '".$name."' AND\n".
               "    `from`<=`to` AND\n".
               "    (`from`<='".$currentTime."' AND\n".
               "    `to`>'".$currentTime."')");

        // Day overflow.
        db_set("UPDATE `detail`\n".
               "SET `visible`='".$currentTime."'\n".
               "WHERE `name` LIKE '".$name."' AND\n".
               "    `from`>`to` AND\n".
               "    (`from`<='".$currentTime."' OR\n".
               "    `to`>'".$currentTime."')");
    }

    if ($oldObjects === true)
    {
        db_set("UPDATE `detail`\n".
               "SET `visible`=NULL\n".
               "WHERE `name` LIKE '".$name."' AND\n".
               "    `from`<=`to` AND\n".
               "    (`from`>'".$currentTime."' OR\n".
               "    `to`<='".$currentTime."')");

        // Day overflow.
        db_set("UPDATE `detail`\n".
               "SET `visible`=NULL\n".
               "WHERE `name` LIKE '".$name."' AND\n".
               "    `from`>`to` AND\n".
               "    (`from`>'".$currentTime."' AND\n".
               "    `to`<='".$currentTime."')");
    }

    if ($newObjects === true ||
        $oldObjects === true)
    {
        $image = imagecreatetruecolor(VIEW_X, VIEW_Y);
        $objectCount = 0;

        foreach ($detail as $object)
        {
            if ($object['visible'] === NULL)
            {
                continue;
            }
            else
            {
                $objectCount++;
            }

            $filePath = dirname(__FILE__)."/../resources/".$object['file'];
            $size = getimagesize($filePath);

            $objectImage = imagecreatefrompng($filePath);
            imagesavealpha($objectImage, true);
            // -1 for $size[1] if used in positioning context because
            // the image size is not zero-based but the coordinate system is.
            imagecopy($image, $objectImage, $object['position_x'], ($object['position_y'] - ($size[1] - 1)), 0, 0, $size[0], $size[1]);
            imagedestroy($objectImage);
        }

        // Apply night effect?
        if ($applyNight === true &&
            $objectCount > 0 &&
            ($currentTime < DAY_FROM ||
             $currentTime >= DAY_TO))
        {
            if (function_exists('imagefilter') === true)
            {
                //imagefilter($image, IMG_FILTER_GRAYSCALE);
                imagefilter($image, IMG_FILTER_NEGATE);
            }
            else
            {
                for ($negate_x = 0; $negate_x < VIEW_X; $negate_x++)
                {
                    for ($negate_y = 0; $negate_y < VIEW_Y; $negate_y++)
                    {
                        $negate_color_pixel = imagecolorat($image, $negate_x, $negate_y);
                        $negate_rgb_pixel = imagecolorsforindex($image, $negate_color_pixel);

                        $negated_pixel = imagecolorallocate($image,
                                                            255 - $negate_rgb_pixel['red'],
                                                            255 - $negate_rgb_pixel['green'],
                                                            255 - $negate_rgb_pixel['blue']);

                        imagesetpixel($image, $negate_x, $negate_y, $negated_pixel);
                    }
                }
            }
        }

        imagepng($image, dirname(__FILE__)."/../cache/".$name.".png");
        imagedestroy($image);
    }


    $coords = db_get_rows("SELECT `detail_id`,\n".
                          "    `from`,\n".
                          "    `to`,\n".
                          "    `visible`,\n".
                          "    `coords`,\n".
                          "    `href`,\n".
                          "    `alt`,\n".
                          "    `title`\n".
                          "FROM `detail_coords`\n".
                          "WHERE `name` LIKE '".$name."'\n".
                          // The first image map area in HTML wins, therefore z-order DESC.
                          "ORDER BY `order_z` DESC\n");

    if (is_array($coords) !== true)
    {
        return 0;
    }

    $newCoords = false;
    $oldCoords = false;

    foreach ($coords as &$coord)
    {
        // No day overflow between 'from' and 'to'?
        if ($coord['from'] <= $coord['to'])
        {
            // Current time between 'from' and 'to'?
            if ($coord['from'] <= $currentTime &&
                $coord['to'] > $currentTime)
            {
                if ($coord['visible'] == "0")
                {
                    $newCoords = true;
                    // Will become accessible.
                    $coord['visible'] = "1";
                }
            }
            else
            {
                if ($coord['visible'] == "1")
                {
                    $oldCoords = true;
                    // Will become inaccessible.
                    $coord['visible'] = "0";
                }
            }
        }
        else
        {
            // Current time between 'from' and 'to'?
            if ($coord['from'] <= $currentTime ||
                $coord['to'] > $currentTime)
            {
                if ($coord['visible'] == "0")
                {
                    $newCoords = true;
                    // Will become accessible.
                    $coord['visible'] = "1";
                }
            }
            else
            {
                if ($coord['visible'] == "1")
                {
                    $oldCoords = true;
                    // Will become inaccessible.
                    $coord['visible'] = "0";
                }
            }
        }
    }

    // Needed because of referenced foreach loop.
    unset($coord);

    if ($newCoords === true)
    {
        db_set("UPDATE `detail_coords`\n".
               "SET `visible`=1\n".
               "WHERE `name` LIKE '".$name."' AND\n".
               "    `from`<=`to` AND\n".
               "    (`from`<='".$currentTime."' AND\n".
               "    `to`>'".$currentTime."')");

        // Day overflow.
        db_set("UPDATE `detail_coords`\n".
               "SET `visible`=1\n".
               "WHERE `name` LIKE '".$name."' AND\n".
               "    `from`>`to` AND\n".
               "    (`from`<='".$currentTime."' OR\n".
               "    `to`>'".$currentTime."')");
    }

    if ($oldCoords === true)
    {
        db_set("UPDATE `detail_coords`\n".
               "SET `visible`=0\n".
               "WHERE `name` LIKE '".$name."' AND\n".
               "    `from`<=`to` AND\n".
               "    (`from`>'".$currentTime."' OR\n".
               "    `to`<='".$currentTime."')");

        // Day overflow.
        db_set("UPDATE `detail_coords`\n".
               "SET `visible`=0\n".
               "WHERE `name` LIKE '".$name."' AND\n".
               "    `from`>`to` AND\n".
               "    (`from`>'".$currentTime."' AND\n".
               "    `to`<='".$currentTime."')");
    }

    if ($newCoords === true ||
        $oldCoords === true)
    {
        $detailAreas = "";

        foreach ($coords as &$coord)
        {
            if ($coord['visible'] == "0")
            {
                continue;
            }

            $objectFound = false;

            foreach ($detail as $object)
            {
                if ($object['visible'] === NULL)
                {
                    continue;
                }

                if ($object['id'] === $coord['detail_id'])
                {
                    $coord['position_x'] = $object['position_x'];
                    $coord['position_y'] = $object['position_y'];
                    $coord['file'] = $object['file'];
                    $objectFound = true;
                    break;
                }
            }

            if ($objectFound !== true)
            {
                // Data inconsistency.
                return -3;
            }

            $coord_xy = explode(",", $coord['coords']);
            $coord_count = count($coord_xy);

            if ($coord_count % 2 != 0)
            {
                continue;
            }

            // Maybe keep size from image processing above to optimize performance.
            $filePath = dirname(__FILE__)."/../resources/".$coord['file'];
            $size = getimagesize($filePath);

            $detailAreas .= "<area shape=\"poly\" coords=\"";

            for ($i = 0; $i < $coord_count; $i += 2)
            {
                $new_x = $coord_xy[$i] + $coord['position_x'];
                // -1 for $size[1] if used in positioning context because
                // the image size is not zero-based but the coordinate system is.
                $new_y = ($coord['position_y'] - ($size[1] - 1)) + $coord_xy[$i+1];

                if ($new_x < 0)
                    $new_x = 0;

                if ($new_x >= VIEW_X)
                    // -1 for VIEW_X if used in positioning context because
                    // the view size is not zero-based but the coordinate system is.
                    $new_x = VIEW_X - 1;

                if ($new_y < 0)
                    $new_y = 0;

                if ($new_y >= VIEW_Y)
                    // -1 for VIEW_Y if used in positioning context because
                    // the view size is not zero-based but the coordinate system is.
                    $new_y = VIEW_Y - 1;

                $detailAreas .= $new_x.",".$new_y.",";
            }

            // Remove trailing ",".
            $detailAreas = substr($detailAreas, 0, -1);

            $detailAreas .= "\" href=\"".$coord['href']."\" alt=\"".$coord['alt']."\" title=\"".$coord['title']."\" />\n";
        }

        // Needed because of referenced foreach loop.
        unset($coord);

        $fp = @fopen(dirname(__FILE__)."/../cache/".$name.".html", "w");

        if ($fp != false)
        {
            if (@fwrite($fp, $detailAreas) == false)
            {
                if (strlen($detailAreas) > 0)
                {
                    return -4;
                }
            }

            @fclose($fp);
        }
    }

    return 0;
}



?>
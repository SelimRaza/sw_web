<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/30/2020
 * Time: 6:13 PM
 */

namespace App\BusinessObject;


class Utility
{
   public static function search($array, $key, $value)
    {
        $results = array();

        if (is_array($array))
        {
            if (isset($array[$key]) && $array[$key] == $value)
                $results[] = $array;

            foreach ($array as $subarray)
                $results = array_merge($results, search($subarray, $key, $value));
        }

        return $results;
    }

}
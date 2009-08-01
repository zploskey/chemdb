<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Helper function to divide without division by zero.
 * @param double|int $num numerator
 * @param double|int $den denominator
 * @return double result
 */
function safe_divide($num, $den) 
{
    if ($den == 0) {
        return 0;
    } else {
        return (double)$num / $den;
    }
}

/**
 * Reverses the sort direction.
 *
 * Changes 'asc' to 'desc' OR 'desc' to 'asc'.  Assumes that the default value is a descending sort
 * if no second parameter is passed. Also converts the returned value to lower case.
 *
 * @return string
 **/
function switch_sort($cur, $default = 'asc')
{
    $cur = strtolower($cur);
    $default = strtolower($default);
    
    if ($default == 'desc') {
        $alt = 'asc';
    } else {
        $alt = 'desc';
    }
    
    if ($cur == $default) {
        return $alt;
    }
    
    return $default;
}

/**
 * Takes an array and returns a string of the elements listed as in a sentence.
 * 
 * Example: 
 * $arr = array('blue', 'red, 'green', 'yellow');
 * echo comma_list($arr)
 * 
 * Yields: "blue, red, green and yellow"
 *
 * @param array $arr array of elements with string representations
 * @return string $str comma separated list, including an and before the last element
 * @author cosmolab
 **/
function comma_str($arr, $do_and = false)
{
    $len = count($arr);
    $str = '';
    for($i = 0; $i < $len; $i++) {
        // don't print a comma before the first item
        if ($i != 0) {
            // put and before the last item
            if ($do_and AND $i == $len - 1) {
                $str .= ' and ';
            } else {
                // everywhere else we print a comma before the item
                $str .= ', ';
            }
        }
        $str .= $arr[$i];
    }
    return $str;
}

function getRealIp()
{ 
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) { 
        // ip is from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP']; 
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // ip is passed from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
    }  else { 
        $ip = $_SERVER['REMOTE_ADDR'];
    } 
    
    return $ip; 
}
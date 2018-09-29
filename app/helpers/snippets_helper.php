<?php  if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * Cast $val to float if it is numeric (is_numeric($val) == TRUE).
 *
 * @param mixed $val value to possibly modify
 * @return mixed $val value cast to float if it is was numeric
 */
function floatcast($val)
{
    if (is_numeric($val)) {
        return (float)$val;
    }
    return $val;
}

/*
 * Call htmlspecialchars and floatcast on contents of $arraylike.
 * Works with nested arrays and Doctrine_Records.
 *
 * @param mixed $arraylike
 * @return mixed $arraylike
 */
function prep_for_output($arraylike)
{
    $is_doctrine_record = $arraylike instanceof Doctrine_Record;
    if ($is_doctrine_record) {
        $arr = $arraylike->toArray();
    } else {
        $arr = $arraylike;
    }

    foreach ($arr as $key => $val) {
        if (is_array($val) || is_object($val)) {
            $arr[$key] = prep_for_output($val);
        } elseif ($val) {
            $arr[$key] = htmlspecialchars(floatcast($val));
        }
    }

    if ($is_doctrine_record) {
        $arr = $arraylike->merge($arr);
    }

    return $arr;
}

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
    }
    return (float)$num / $den;
}

/**
 * Reverses the sort direction.
 *
 * Changes 'asc' to 'desc' OR 'desc' to 'asc'.  Assumes that the default value is a descending sort
 * if no second parameter is passed. Also converts the returned value to lower case.
 *
 * @param string $cur Current sort direction ('desc' or 'asc')
 * @param string $default Default sort direction
 * @return string Opposite sort direction
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
 * @param array $arr Strings to convert to a comma and space separated string
 * @param bool $do_and Should the last comma be converted to " and"
 * @return string $str Comma separated list, optionally including " and "
 *                     before the last element.
 **/
function comma_str($arr, $do_and = false)
{
    $len = count($arr);
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        // don't print a comma before the first item
        if ($i != 0) {
            // put and before the last item
            if ($do_and and $i == $len - 1) {
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
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    if ($ip == '::1') {
        $ip = '172.28.21.187';
    }
    return $ip;
}

/**
 * Squares a number (an int or float).
 * @param number $x Number to square
 * @param number $x * $x
 */
function square($x)
{
    return $x * $x;
}

/**
 * A poor man's sum of squares.
 * @param array $vals Numeric values
 * @return number
 **/
function sum_of_squares($vals)
{
    return array_sum(array_map('square', $vals));
}

/**
 * Sum of squared errors.
 *
 * @param array $vals Numeric values
 * @param float $xbar Mean value of $vals
 * @return float Sum of squared errors of $vals
 */
function sse($vals, $xbar)
{
    foreach ($vals as $x) {
        $diffs[] = $x - $xbar;
    }
    return sum_of_squares($diffs);
}

/**
 * Calculates the mean of a dataset. Also returns the standard deviation
 * as a second parameter.
 * By default returns the sample standard deviation of $vals. You can change
 * the degrees of freedom w/ the second argument.
 *
 * @param array $vals Numeric values
 * @param int $dof Degrees of freedom
 * @return float Mean standard deviation
 */
function meanStdDev($vals, $dof = 1)
{
    $n = count($vals);
    if ($n == 1) {
        return 0;
    } elseif ($n - $dof <= 0) {
        return null;
    }
    $xbar = array_sum($vals) / $n;
    return array($xbar, sqrt(sse($vals, $xbar) / ($n - $dof)));
}

/**
 * Mean of data set using safe_divide.
 *
 * @param array $vals Numeric values
 * @return float Mean of $vals
 */
function mean($vals)
{
    return safe_divide(array_sum($vals), count($vals));
}

/**
 * Round a value to the a multiple.
 *
 * @param number $num Number to round
 * @param number $toNearest Multiple to round to. Not very useful if not an int
 */
function roundToNearest($num, $toNearest = 5)
{
    $nearest = abs($toNearest);
    return round(safe_divide($num, $nearest)) * $nearest;
}

/**
 * Round a value down to the nearest multiple.
 *
 * @param number $num Number to round
 * @param number $toNearest Multiple to round to. Not very useful if not an int
 */
function roundDownToNearest($num, $toNearest = 5)
{
    $nearest = abs($toNearest);
    return floor(safe_divide($num, $nearest)) * $nearest;
}

/**
 * Remove spaces from string.
 *
 * @param string $string
 * @return string String with spaces removed.
 */
function strip_spaces($string)
{
    return str_replace(' ', '', $string);
}

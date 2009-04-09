<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
	
	if ($default == 'desc')
	{
		$alt = 'asc';
	}
	else
	{
		$alt = 'desc';
	}
	
	if ($cur == $default)
	{
		return $alt;
	}
	
	return $default;
}


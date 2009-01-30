<?php 
/*

Customized functions should be put in
./system/application/libraries/MY_Form_validation.php
if they relate to validation, or in
./system/application/libraries/MY_Model.php
if they require post information.

Config sections are *named for the controller they are used to validate*.

*/

$config['projects'] = array(
	array(
		'field' => 'name',
		'label' => 'Name',
		'rules' => 'trim|required|alpha_dash|callback_is_unique[projects.name]'),
	array(
		'field' => 'description',
		'label' => 'Description',
		'rules' => 'trim|htmlentities'));

$config['samples'] = array(
	array(
		'field' => 'name',
		'label' => 'Name',
		'rules' => 'trim|required|alpha_dash|callback_is_unique[samples.name]'),
	array(
		'field' => 'latitude',
		'label' => 'Latitude',
		'rules' => 'trim|numeric'),
	array(
		'field' => 'longitude',
		'label' => 'Longitude',
		'rules' => 'trim|numeric'),
	array(
		'field' => 'altitude',
		'label' => 'Altitude',
		'rules' => 'trim|numeric'),
	array(
		'field' => 'shield_factor',
		'label' => 'Shield Factor',
		'rules' => 'trim|numeric'),
	array(
		'field' => 'depth_top',
		'label' => 'Depth Top',
		'rules' => 'trim|numeric'),
	array(
		'field' => 'depth_bottom',
		'label' => 'Depth Bottom',
		'rules' => 'trim|numeric'),
	array(
		'field' => 'density',
		'label' => 'Density',
		'rules' => 'trim|numeric'));

/* End of file form_validation.php */
/* Location: ./system/application/config/form_validation.php */
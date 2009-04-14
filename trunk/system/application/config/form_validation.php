<?php 
/*

Customized functions should be put in
./system/application/libraries/MY_Form_validation.php
if they relate to validation, or in
./system/application/libraries/MY_Model.php
if they require post information.

Config sections are named for the controller they are used to validate, or the
function in the controller they validate.

*/

$config['projects'] = array(
	array(
		'field' => 'name',
		'label' => 'Name',
		'rules' => 'trim|required|alpha_dash|callback_is_unique[project.name]'),
	array(
		'field' => 'description',
		'label' => 'Description',
		'rules' => 'trim|htmlentities'));

$config['samples'] = array(
	array(
		'field' => 'name',
		'label' => 'Name',
		'rules' => 'trim|required|alpha_dash|callback_is_unique[sample.name]'),
	array(
		'field' => 'latitude',
		'label' => 'Latitude',
		'rules' => 'trim|numeric|callback_valid_latlong'),
	array(
		'field' => 'longitude',
		'label' => 'Longitude',
		'rules' => 'trim|numeric|callback_valid_latlong'),
	array(
		'field' => 'altitude',
		'label' => 'Altitude',
		'rules' => 'trim|numeric'),
	array(
		'field' => 'shield_factor',
		'label' => 'Shield Factor',
		'rules' => 'trim|numeric|callback_valid_shield_factor'),
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

$config['batches'] = array(
	array(
		'field' => 'id',
		'label' => 'ID',
		'rules' => 'trim|is_natural_no_zero'),
	array(
		'field' => 'description',
		'label' => 'Description',
		'rules' => 'trim|htmlspecialchars'),
	array(
		'field' => 'owner',
		'label' => 'Initials',
		'rules' => 'trim|htmlspecialchars'),
	array(
		'field' => 'numsamples',
		'label' => '# of Samples',
		'rules' => 'is_natural_no_zero'));

$config['load_samples'] = array(
	array(
		'field' => 'be_carrier_name',
		'label' => 'Beryllium Carrier Name',
		'rules' => 'alphanumeric'),
	array(
		'field' => 'batch_notes',
		'label' => 'Notes',
		'rules' => 'trim|htmlspecialchars'));

/* End of file form_validation.php */
/* Location: ./system/application/config/form_validation.php */
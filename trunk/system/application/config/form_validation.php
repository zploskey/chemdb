<?php 

$config = array(
	'project' => array(
		array(
			'field' => 'name',
			'label' => 'Name',
			'rules' => 'required|alpha_dash|callback_name_exists'
		)
	)	
);
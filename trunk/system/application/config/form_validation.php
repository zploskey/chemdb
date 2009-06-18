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
		'label' => 'name',
		'rules' => 'trim|required|alpha_dash|callback_is_unique[project.name]'),
	array(
		'field' => 'description',
		'label' => 'description',
		'rules' => 'trim|htmlentities'),
);

$config['samples'] = array(
	array(
		'field' => 'sample[name]',
		'label' => 'name',
		'rules' => 'trim|required|alpha_dash|callback_is_unique[sample.name]'),
	array(
		'field' => 'sample[latitude]',
		'label' => 'latitude',
		'rules' => 'trim|numeric|callback_valid_latlong'),
	array(
		'field' => 'sample[longitude]',
		'label' => 'longitude',
		'rules' => 'trim|numeric|callback_valid_latlong'),
	array(
		'field' => 'sample[altitude]',
		'label' => 'altitude',
		'rules' => 'trim|numeric'),
	array(
		'field' => 'sample[shield_factor]',
		'label' => 'shield factor',
		'rules' => 'trim|numeric|callback_valid_shield_factor'),
	array(
		'field' => 'sample[depth_top]',
		'label' => 'depth top',
		'rules' => 'trim|numeric'),
	array(
		'field' => 'sample[depth_bottom]',
		'label' => 'depth bottom',
		'rules' => 'trim|numeric'),
	array(
		'field' => 'sample[density]',
		'label' => 'density',
		'rules' => 'trim|numeric'),
);

$config['batches'] = array(
	array(
		'field' => 'id',
		'label' => 'ID',
		'rules' => 'trim|is_natural_no_zero'),
	array(
		'field' => 'description',
		'label' => 'description',
		'rules' => 'trim|htmlspecialchars'),
	array(
		'field' => 'owner',
		'label' => 'Initials',
		'rules' => 'trim|htmlspecialchars'),
	array(
		'field' => 'numsamples',
		'label' => '# of samples',
		'rules' => 'trim|is_natural_no_zero'),
);

$config['load_samples'] = array(
    array(
        'field' => 'batch[notes]',
        'label' => 'notes',
        'rules' => 'trim|htmlspecialchars'),
    array(
        'field' => 'batch[be_carrier_id]',
        'label' => 'beryllium carrier id',
        'rules' => 'trim|is_natural'),
	array(
		'field' => 'batch[al_carrier_id]',
		'label' => 'aluminum carrier id',
		'rules' => 'trim|is_natural'),
    array(
        'field' => 'batch[wt_be_carrier_init]',
        'label' => 'Be carrier intial weight',
        'rules' => 'trim|numeric|abs'),
    array(
        'field' => 'batch[wt_be_carrier_final]',
        'label' => 'Be carrier final weight',
        'rules' => 'trim|numeric|abs'),
    array(
        'field' => 'batch[wt_al_carrier_init]',
        'label' => 'Al carrier intial weight ',
        'rules' => 'trim|numeric|abs'),
    array(
        'field' => 'batch[wt_al_carrier_final]',
        'label' => 'Al carrier final wt',
        'rules' => 'trim|numeric|abs'),
    array(
        'field' => 'sample_name[]',
        'label' => 'sample name',
        'rules' => 'trim|alpha_dash'),
    array(
        'field' => 'sample_type[]',
        'label' => 'sample type',
        'rules' => 'trim|alphanumeric'),
    array(
        'field' => 'diss_bottle_id[]',
        'label' => 'diss bottle id',
        'rules' => 'trim|is_natural'),
    array(
        'field' => 'wt_diss_bottle_tare[]',
        'label' => 'Wt. bottle tare',
        'rules' => 'trim|numeric|abs'),
    array(
        'field' => 'wt_diss_bottle_sample[]',
        'label' => 'Wt. bottle + sample',
        'rules' => 'trim|numeric|abs'),
    array(
        'field' => 'wt_be_carrier[]',
        'label' => 'Wt. Be carrier soln.',
        'rules' => 'trim|alpha_dash'),
    array(
        'field' => 'wt_al_carrier[]',
        'label' => 'Wt. Al carrier soln.',
        'rules' => 'trim|numeric|abs'),
);

$config['add_solution_weights'] = array(
    array(
        'field' => 'wt_diss_bottle_total[]',
        'label' => 'total weight',
        'rules' => 'trim|numeric'),
    array(
        'field' => 'batch[notes]',
        'label' => 'batch notes',
        'rules' => 'trim|htmlentities'),
);

$config['add_split_weights'] = array(
    array(
        'field' => 'batch[notes]',
        'label' => 'batch notes',
        'rules' => 'trim|htmlentities'),
    array(
        'field' => 'split_bkr[]',
        'label' => 'split beaker ID',
        'rules' => 'trim|is_natural'),
    array(
        'field' => 'bkr_tare[]',
        'label' => 'beaker tare wt.',
        'rules' => 'trim|numeric'),
    array(
        'field' => 'bkr_split[]',
        'label' => 'beaker + split wt.',
        'rules' => 'trim|numeric'),
);

$config['add_icp_weights'] = array(
    array(
        'field' => 'batch[notes]',
        'label' => 'batch notes',
        'rules' => 'trim|htmlentities'),
    array(
        'field' => 'batch[icp_date]',
        'label' => 'ICP date',
        'rules' => 'trim|alpha_dash|callback_valid_date'),
    array(
        'field' => 'tot_wts[]',
        'label' => 'beaker + ICP solution wt.',
        'rules' => 'trim|numeric'),
);

/* End of file form_validation.php */
/* Location: ./system/application/config/form_validation.php */
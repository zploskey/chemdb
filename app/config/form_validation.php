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
        'rules' => 'trim|required|alpha_dot_dash|callback__is_unique[project.name.3]'),
    array(
        'field' => 'description',
        'label' => 'description',
        'rules' => 'trim|htmlentities'),
    array(
        'field' => 'samp',
        'label' => 'sample',
        'rules' => 'trim|callback__sample_exists'),
);

$config['SplitBkr'] = array(
    array(
        'field' => 'bkr_number',
        'label' => 'beaker number',
        'rules' => 'trim|required|alpha_dot_dash|callback__is_unique[SplitBkr.bkr_number.4]'),
);

$config['DissBottle'] = array(
    array(
        'field' => 'bottle_number',
        'label' => 'bottle number',
        'rules' => 'trim|required|alpha_dot_dash|callback__is_unique[DissBottle.bottle_number.4]'),
);

$config['BaseCarrier'] = array(
    array(
        'field' => 'carrier[id]',
        'label' => 'ID',
        'rules' => 'is_natural_no_zero'),
    array(
        'field' => 'carrier[name]',
        'label' => 'name',
        'rules' => 'trim|required|alpha_dot_dash'),
    array(
        'field' => 'carrier[al_conc]',
        'label' => 'Al concentration',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'carrier[del_al_conc]',
        'label' => 'Al concentration uncertainty',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'carrier[in_service_date]',
        'label' => 'in-service date',
        'rules' => 'trim|valid_date'),
    array(
        'field' => 'carrier[mfg_lot_no]',
        'label' => 'manufacturer lot no.',
        'rules' => 'trim|htmlentities'),
    array(
        'field' => 'carrier[notes]',
        'label' => 'notes',
        'rules' => 'trim|htmlentities'),
    array(
        'field' => 'carrier[in_use]',
        'label' => 'In use?',
        'rules' => 'integer'),
);

$config['AlCarrier'] = array(
    array(
        'field' => 'carrier[r26to27]',
        'label' => 'Al-26 / Al-27 ratio',
        'rules' => 'is_numeric'),
    array(
        'field' => 'carrier[r26to27_error]',
        'label' => 'Al-26 / Al-27 ratio uncertainty',
        'rules' => 'is_numeric'),
);
$config['AlCarrier'] =
    array_merge($config['AlCarrier'], $config['BaseCarrier']);

$config['BeCarrier'] = array(
    array(
        'field' => 'carrier[r10to9]',
        'label' => 'Be-10 / Be-9 ratio',
        'rules' => 'is_numeric'),
    array(
        'field' => 'carrier[r10to9_error]',
        'label' => 'Be-10 / Be-9 ratio uncertainty',
        'rules' => 'is_numeric'),
    array(
        'field' => 'carrier[be_conc]',
        'label' => 'Be concentration',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'carrier[del_be_conc]',
        'label' => 'Be concentration uncertainty',
        'rules' => 'trim|is_numeric'),
);
$config['BeCarrier'] =
    array_merge($config['BeCarrier'], $config['BaseCarrier']);

$config['samples'] = array(
    array(
        'field' => 'sample[name]',
        'label' => 'name',
        'rules' => 'trim|required|alpha_dot_dash|callback__is_unique[sample.name.3]'),
    array(
        'field' => 'proj[]',
        'label' => 'project',
        'rules' => 'trim|is_natural_no_zero'),
    array(
        'field' => 'sample[latitude]',
        'label' => 'latitude',
        'rules' => 'trim|latlong'),
    array(
        'field' => 'sample[longitude]',
        'label' => 'longitude',
        'rules' => 'trim|latlong'),
    array(
        'field' => 'sample[altitude]',
        'label' => 'altitude',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'sample[antarctic]',
        'field' => 'antarctic',
        'rules' => 'callback__valid_antarctic'),
    array(
        'field' => 'sample[shield_factor]',
        'label' => 'shield factor',
        'rules' => 'trim|shield_factor'),
    array(
        'field' => 'sample[depth_top]',
        'label' => 'depth top',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'sample[depth_bottom]',
        'label' => 'depth bottom',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'sample[density]',
        'label' => 'density',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'sample[erosion_rate]',
        'label' => 'erosion rate',
        'rules' => 'trim|is_numeric'),
);

$config['batches'] = array(
    array(
        'field' => 'id',
        'label' => 'ID',
        'rules' => 'trim|is_natural_no_zero'),
    array(
        'field' => 'description',
        'label' => 'description',
        'rules' => 'trim|htmlentities'),
    array(
        'field' => 'owner',
        'label' => 'Initials',
        'rules' => 'trim|htmlentities'),
    array(
        'field' => 'numsamples',
        'label' => '# of samples',
        'rules' => 'trim|is_natural_no_zero'),
);

$config['load_samples'] = array(
    array(
        'field' => 'notes',
        'label' => 'notes',
        'rules' => 'trim|htmlentities'),
    array(
        'field' => 'be_carrier_id',
        'label' => 'beryllium carrier id',
        'rules' => 'trim|is_natural'),
    array(
        'field' => 'al_carrier_id',
        'label' => 'aluminum carrier id',
        'rules' => 'trim|is_natural'),
    array(
        'field' => 'wt_be_carrier_init',
        'label' => 'Be carrier initial weight',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'wt_be_carrier_final',
        'label' => 'Be carrier final weight',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'wt_al_carrier_init',
        'label' => 'Al carrier initial weight ',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'wt_al_carrier_final',
        'label' => 'Al carrier final wt',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'sample_name[]',
        'label' => 'sample name',
        'rules' => 'trim|alpha_dot_dash'),
    array(
        'field' => 'sample_type[]',
        'label' => 'sample type',
        'rules' => 'trim|alpha'),
    array(
        'field' => 'diss_bottle_id[]',
        'label' => 'diss bottle id',
        'rules' => 'trim|is_natural'),
    array(
        'field' => 'wt_diss_bottle_tare[]',
        'label' => 'Wt. bottle tare',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'wt_diss_bottle_sample[]',
        'label' => 'Wt. bottle + sample',
        'rules' => 'trim|is_numeric'),
    array(
        'field' => 'wt_be_carrier[]',
        'label' => 'Wt. Be carrier soln.',
        'rules' => 'trim|is_numeric|abs'),
    array(
        'field' => 'wt_al_carrier[]',
        'label' => 'Wt. Al carrier soln.',
        'rules' => 'trim|is_numeric|abs'),
);

$config['add_solution_weights'] = array(
    array(
        'field' => 'wt_diss_bottle_total[]',
        'label' => 'total weight',
        'rules' => 'trim|is_numeric|abs'),
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
        'rules' => 'trim|is_numeric|abs'),
    array(
        'field' => 'bkr_split[]',
        'label' => 'beaker + split wt.',
        'rules' => 'trim|is_numeric|abs'),
);

$config['add_icp_weights'] = array(
    array(
        'field' => 'batch[notes]',
        'label' => 'batch notes',
        'rules' => 'trim|htmlentities'),
    array(
        'field' => 'batch[icp_date]',
        'label' => 'ICP date',
        'rules' => 'trim|valid_date'),
    array(
        'field' => 'tot_wts[]',
        'label' => 'beaker + ICP solution wt.',
        'rules' => 'trim|is_numeric|abs'),
);

$config['add_icp_results'] = array(
    array(
        'field' => 'notes',
        'label' => 'notes',
        'rules' => 'trim|htmlentities'),
);

$config['icp_quality_control'] = array(
    array(
        'field' => 'use_be[]',
        'label' => 'Be OK?',
        'rules' => 'is_natural'),
    array(
        'field' => 'use_al[]',
        'label' => 'Al OK?',
        'rules' => 'is_natural'),
    array(
        'field' => 'notes',
        'label' => 'notes',
        'rules' => 'trim|htmlentities'),
);

// Aluminum checks

$config['al_new_batch'] = array(
    array(
        'field' => 'prep_date',
        'label' => 'date',
        'rules' => 'required|trim|alpha_dash|valid_date'),
    array(
        'field' => 'numsamples',
        'label' => 'number of samples',
        'rules' => 'required|is_natural_no_zero'),
    array(
        'field' => 'owner',
        'label' => 'batch owner',
        'rules' => 'required|trim|alpha_dash'),
    array(
        'field' => 'description',
        'label' => 'description',
        'rules' => 'trim|htmlentities'),
);

$config['al_sample_loading'] = array(
    array(
        'field' => 'sample_name[]',
        'label' => 'sample name',
        'rules' => 'trim|alpha_dot_dash'),
    array(
        'field' => 'bkr_number[]',
        'label' => 'beaker number',
        'rules' => 'trim|htmlentities'), // this is a string in the db...
    array(
        'field' => 'wt_bkr_tare[]',
        'label' => 'beaker tare weight',
        'rules' => 'trim|is_numeric|abs'),
    array(
        'field' => 'wt_bkr_sample[]',
        'label' => 'beaker + sample weight',
        'rules' => 'trim|is_numeric|abs'),
    array(
        'field' => 'notes[]',
        'label' => 'notes',
        'rules' => 'trim|htmlentities'),
);

$config['al_add_solution_weights'] = array(
    array(
        'field' => 'wt_bkr_soln[]',
        'label' => 'Bkr + soln.',
        'rules' => 'trim|is_numeric|abs'),
    array(
        'field' => 'addl_dil_factor[]',
        'label' => "Add'l DF",
        'rules' => 'trim|is_numeric|abs'),
    array(
        'field' => 'notes[]',
        'label' => 'notes',
        'rules' => 'trim|htmlentities')
);

$config['al_add_icp_data'] = array(
    array(
        'field' => 'icp_be[]',
        'label' => 'ICP [Be]',
        'rules' => 'trim|is_numeric|negis0'),
    array(
        'field' => 'icp_ca[]',
        'label' => 'ICP [Ca]',
        'rules' => 'trim|is_numeric|negis0'),
    array(
        'field' => 'icp_ti[]',
        'label' => 'ICP [Ti]',
        'rules' => 'trim|is_numeric|negis0'),
    array(
        'field' => 'icp_fe[]',
        'label' => 'ICP [Fe]',
        'rules' => 'trim|is_numeric|negis0'),
    array(
        'field' => 'icp_al[]',
        'label' => 'ICP [Al]',
        'rules' => 'trim|is_numeric|negis0'),
    array(
        'field' => 'icp_mg[]',
        'label' => 'ICP [Mg]',
        'rules' => 'trim|is_numeric|negis0'),
    array(
        'field' => 'icp_date',
        'label' => 'ICP date',
        'rules' => 'trim|valid_date'),
);

$config['al_quick_add'] = array(
    array(
        'field' => 'sample_name',
        'label' => 'sample name',
        'rules' => 'trim|required|alpha_dash'),
    array(
        'field' => 'icp_al',
        'label' => '[Al]',
        'rules' => 'trim|required|is_numeric|abs'),
    array(
        'field' => 'icp_fe',
        'label' => '[Fe]',
        'rules' => 'trim|required|is_numeric|abs'),
    array(
        'field' => 'icp_ti',
        'label' => '[Ti]',
        'rules' => 'trim|required|is_numeric|abs'),
);

/* End of file form_validation.php */
/* Location: ./system/application/config/form_validation.php */

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
        'rules' => 'trim|required|alpha_dot_dash|callback__is_unique[project.name]'),
    array(
        'field' => 'description',
        'label' => 'description',
        'rules' => 'trim|htmlentities'),
    array(
        'field' => 'samp',
        'label' => 'sample',
        'rules' => 'trim|callback__sample_exists'),
);

$config['samples'] = array(
    array(
        'field' => 'sample[name]',
        'label' => 'name',
        'rules' => 'trim|required|alpha_dot_dash|callback__is_unique[sample.name]'),
    array(
        'field' => 'proj[]',
        'label' => 'project',
        'rules' => 'trim|noreq_natural_no_zero'),
    array(
        'field' => 'sample[latitude]',
        'label' => 'latitude',
        'rules' => 'trim|noreq_latlong'),
    array(
        'field' => 'sample[longitude]',
        'label' => 'longitude',
        'rules' => 'trim|noreq_latlong'),
    array(
        'field' => 'sample[altitude]',
        'label' => 'altitude',
        'rules' => 'trim|noreq_numeric'),
    array(
        'field' => 'sample[antarctic]',
        'field' => 'antarctic',
        'rules' => 'callback__valid_antarctic'),
    array(
        'field' => 'sample[shield_factor]',
        'label' => 'shield factor',
        'rules' => 'trim|noreq_shield_factor'),
    array(
        'field' => 'sample[depth_top]',
        'label' => 'depth top',
        'rules' => 'trim|noreq_numeric'),
    array(
        'field' => 'sample[depth_bottom]',
        'label' => 'depth bottom',
        'rules' => 'trim|noreq_numeric'),
    array(
        'field' => 'sample[density]',
        'label' => 'density',
        'rules' => 'trim|noreq_numeric'),
    array(
        'field' => 'sample[erosion_rate]',
        'label' => 'erosion rate',
        'rules' => 'trim|noreq_numeric'),
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
        'rules' => 'trim|noreq_numeric'),
    array(
        'field' => 'wt_be_carrier_final',
        'label' => 'Be carrier final weight',
        'rules' => 'trim|noreq_numeric'),
    array(
        'field' => 'wt_al_carrier_init',
        'label' => 'Al carrier initial weight ',
        'rules' => 'trim|noreq_numeric'),
    array(
        'field' => 'wt_al_carrier_final',
        'label' => 'Al carrier final wt',
        'rules' => 'trim|noreq_numeric'),
    array(
        'field' => 'sample_name[]',
        'label' => 'sample name',
        'rules' => 'trim|noreq_alpha_dot_dash'),
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
        'rules' => 'trim|noreq_numeric'),
    array(
        'field' => 'wt_diss_bottle_sample[]',
        'label' => 'Wt. bottle + sample',
        'rules' => 'trim|noreq_numeric'),
    array(
        'field' => 'wt_be_carrier[]',
        'label' => 'Wt. Be carrier soln.',
        'rules' => 'trim|noreq_numeric|abs'),
    array(
        'field' => 'wt_al_carrier[]',
        'label' => 'Wt. Al carrier soln.',
        'rules' => 'trim|noreq_numeric|abs'),
);

$config['add_solution_weights'] = array(
    array(
        'field' => 'wt_diss_bottle_total[]',
        'label' => 'total weight',
        'rules' => 'trim|noreq_numeric|abs'),
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
        'rules' => 'trim|noreq_numeric|abs'),
    array(
        'field' => 'bkr_split[]',
        'label' => 'beaker + split wt.',
        'rules' => 'trim|noreq_numeric|abs'),
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
        'rules' => 'trim|noreq_numeric|abs'),
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
        'rules' => 'trim|noreq_alpha_dot_dash'),
    array(
        'field' => 'bkr_number[]',
        'label' => 'beaker number',
        'rules' => 'trim|htmlentities'), // this is a string in the db...
    array(
        'field' => 'wt_bkr_tare[]',
        'label' => 'beaker tare weight',
        'rules' => 'trim|noreq_numeric|abs'),
    array(
        'field' => 'wt_bkr_sample[]',
        'label' => 'beaker + sample weight',
        'rules' => 'trim|noreq_numeric|abs'),
    array(
        'field' => 'notes[]',
        'label' => 'notes',
        'rules' => 'trim|htmlentities'),
);

$config['al_add_solution_weights'] = array(
    array(
        'field' => 'wt_bkr_soln[]',
        'label' => 'Bkr + soln.',
        'rules' => 'trim|noreq_numeric|abs'),
    array(
        'field' => 'addl_dil_factor[]',
        'label' => "Add'l DF",
        'rules' => 'trim|noreq_numeric|abs'),
    array(
        'field' => 'notes[]',
        'label' => 'notes',
        'rules' => 'trim|htmlentities')
);

$config['al_add_icp_data'] = array(
    array(
        'field' => 'icp_be[]',
        'label' => 'ICP [Be]',
        'rules' => 'trim|noreq_numeric|negis0'),
    array(
        'field' => 'icp_ti[]',
        'label' => 'ICP [Ti]',
        'rules' => 'trim|noreq_numeric|negis0'),
    array(
        'field' => 'icp_fe[]',
        'label' => 'ICP [Fe]',
        'rules' => 'trim|noreq_numeric|negis0'),
    array(
        'field' => 'icp_al[]',
        'label' => 'ICP [Al]',
        'rules' => 'trim|noreq_numeric|negis0'),
    array(
        'field' => 'icp_mg[]',
        'label' => 'ICP [Mg]',
        'rules' => 'trim|noreq_numeric|negis0'),
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
        'rules' => 'trim|required|numeric|abs'),
    array(
        'field' => 'icp_fe',
        'label' => '[Fe]',
        'rules' => 'trim|required|numeric|abs'),
    array(
        'field' => 'icp_ti',
        'label' => '[Ti]',
        'rules' => 'trim|required|numeric|abs'),    
);

/* End of file form_validation.php */
/* Location: ./system/application/config/form_validation.php */

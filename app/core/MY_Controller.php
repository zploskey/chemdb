<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        
        $this->load->library('form_validation');

        // Global error settings:
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        // Uncommenting the following enables the profiler, which gives stats
        // for our queries and page loads, shows POST data, etc.
        // $this->output->enable_profiler(TRUE);
    }
    
    // ----------
    // CALLBACKS:
    // ----------
    
    function _is_natural_no_zero($val)
    {
        if ($val == '' || $this->form_validation->is_natural_no_zero($val)) {
            return true;
        }
        $this->form_validation->set_message('_is_natural', 
            'The $s field must contain only positive integers.');
        return false;
    }
    
    function _is_unique($val, $field)
    {
        $id = $this->uri->segment(3, null);
        
        if ($this->form_validation->is_unique($val, $field, $id)) {
            return true;
        }
        $this->form_validation->set_message('_is_unique', 
            'The %s field already exists in the database, please choose a different one.');
        return false;
    }
    
    function _noreq_alpha_dot_dash($val)
    {
        if ($val == '' || $this->form_validation->alpha_dot_dash($val)) {
            return true;
        }
        $this->form_validation->set_message(
            '_noreq_alpha_dot_dash', $this->lang->line('alpha_dot_dash'));
        return false;
    }

    /*
     * Returns true if value is not greater than 180.
     */
    function _valid_latlong($val)
    {
        if ($val == '' || is_numeric($val) && abs($val) <= 180) {
            return true;
        }
        $this->form_validation->set_message('_valid_latlong',
                            'The %s field must be from -180 to 180.');
        return false;
    }

    function _valid_shield_factor($val)
    {
        if ($val == '' || is_numeric($val) && abs($val) <= 1) {
            return true;
        }
        $this->form_validation->set_message('_valid_shield_factor',
                                'The %s field must be from 0 to 1.');
        return false;
    }

    function _num($val)
    {
        if ($val == '' || is_numeric($val)) {
            return true;
        }
        
        $this->form_validation->set_message('_num', 'The %s field must be a number.');
        return false;
    }

}

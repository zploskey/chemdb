<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends Controller
{

    function __construct()
    {
        parent::Controller();
        
        $this->load->library('form_validation');

        // Global error settings:
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->form_validation->set_message('is_unique', 
            'The %s field must be unique. Please choose another.');
        
        // Uncommenting the following enables the profiler, which gives stats
        // for our queries and page loads, shows POST data, etc.
        // $this->output->enable_profiler($this->config->item('show_profiler'));
    }
    
    // ----------
    // CALLBACKS:
    // ----------
    
    function _is_natural($val)
    {
        if ($val == '') {
            return true;
        }
        
        if (is_numeric($val) && $val != 0) {
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
    
    function _noreq_alpha_dash($val)
    {
        if ($val == '') {
            return true;
        }
        
        if ($this->form_validation->alpha_dash($val)) {
            return true;
        }
        
        $this->form_validation->set_message('_noreq_alpha_dash', 
            'The %s field may only contain alpha-numeric characters, underscores, and dashes.');
    }

    /*
     * Returns true if value is not greater than 180.
     */
    function _valid_latlong($val)
    {
        if ($val == '') {
            return true;
        }
        
        if (is_numeric($val) && (abs($val) <= 180)) {
            return true;
        }

        $this->form_validation->set_message('_valid_latlong', 'The %s field must be from -180 to 180.');
        return FALSE;
    }

    function _valid_shield_factor($val)
    {
        if ($val == '') {
            return true;
        }
        if (is_numeric($val) && abs($val) <= 1) {
            return TRUE;
        }

        $this->form_validation->set_message('_valid_shield_factor', 'The %s field must be from 0 to 1.');
        return FALSE;
    }
    
    function _num($val)
    {
        if ($val == '') {
            return true;
        }
        if (is_numeric($val)) {
            return true;
        }
        $this->form_validation->set_message('_num', 'The %s field must be a number.');
        return false;
    }

}
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
        $this->output->enable_profiler($this->config->item('show_profiler'));
    }

}
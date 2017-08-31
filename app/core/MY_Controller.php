<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');

        // Global error settings:
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

        // Uncommenting the following enables the profiler, which gives stats
        // for our queries and page loads, shows POST data, etc.
        // $this->output->enable_profiler(TRUE);
    }

    public function _is_unique($val, $field_and_id_uriseg)
    {
        list($table, $column, $id_uriseg) = explode('.', $field_and_id_uriseg);
        $id = $this->uri->segment($id_uriseg, null);

        if ($this->form_validation->is_unique_or_existing($val, $table, $column, $id)) {
            return true;
        }

        $this->form_validation->set_message(
            '_is_unique',
            'The %s field already exists in the database, please choose a different one.'
        );
        return false;
    }
}

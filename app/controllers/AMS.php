<?php

class AMS extends MY_Controller
{
    public function index()
    {
        $data = array(
            'title' => 'AMS Measurements',
            'subtitle' => 'Manage AMS measurements',
            'main' => 'ams/index',
        );
        $this->load->view('template', $data);
    }
}

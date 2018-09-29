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

    public function labs()
    {
        $data = array(
            'title' => 'AMS Labs',
            'subtitle' => 'Manage AMS laboratories',
            'main' => 'ams/labs',
        );
        $labs = Doctrine_Query::create()
            ->from('AmsLab')
            ->orderBy('name asc')
            ->execute();
        $data['labs'] = $labs;
        $this->load->view('template', $data);
    }

}

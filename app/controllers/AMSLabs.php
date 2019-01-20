<?php

class AMSLabs extends MY_Controller
{
    public function index()
    {
        $data = array(
            'title' => 'AMS Labs',
            'subtitle' => 'Manage AMS laboratories',
            'main' => 'ams/lab/index',
        );
        $labs = Doctrine_Query::create()
            ->from('AmsLab')
            ->orderBy('name asc')
            ->execute();
        $data['labs'] = $labs;
        $this->load->view('template', $data);
    }

    public function add()
    {
        $this->edit();
    }

    public function edit($id = null)
    {
        $lab = $id === null ? new AmsLab() : self::_find($id);
        if ($this->input->post()) {
            $valid = $this->_validate();
            $lab = $lab->merge($this->input->post('lab'));
            if ($valid) {
                $lab->save();
                redirect("ams/lab/view/$lab->id");
            }
        }

        $action = $id !== null ? 'Edit' : 'Add';
        $this->load->view(
            'template',
            array(
                'title' => $action . ' AMS Lab',
                'main' => 'ams/lab/edit',
                'subtitle' => "AMS Lab: $lab->name",
                'lab' => $lab,
            )
        );
    }

    public function view($id)
    {
        $lab = prep_for_output(self::_find($id));
        $this->load->view(
            'template',
            array(
                'title' => 'View AMS Lab',
                'subtitle' => "AMS Lab: $lab->name",
                'main' => 'ams/lab/view',
                'lab' => $lab,
            )
        );
    }

    private static function _find($id)
    {
        $lab = Doctrine_Core::getTable('AmsLab')->find($id);
        if (!$lab) {
            show_404();
        }
        return $lab;
    }

    private function _validate()
    {
        return $this->form_validation->run('AmsLab');
    }
}

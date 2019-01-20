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
            $lab = $lab->merge($this->input->post('lab'));
            if ($this->_validate()) {
                $lab->save();
                if ($id === null) {
                    redirect("ams/lab/edit/$lab->id");
                }
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
        $lab = Doctrine_Core::getTable('AmsLab')->find($id);
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
        return Doctrine_Core::getTable('AmsLab')->find($id);
    }

    private function _validate()
    {
        return $this->form_validation->run('AmsLab');
    }
}

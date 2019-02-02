<?php

class AMSStandardSeries extends MY_Controller
{
    const SUPPORTED_ELEMENTS = array(
        'Al',
        'Be',
    );

    const TABLE_SUFFIX = 'StdSeries';

    protected static function _validElementOr404($element)
    {
        $element = ucfirst($element);
        if (!in_array($element, self::SUPPORTED_ELEMENTS)) {
            show_404();
        }
        return $element;
    }

    protected function _landing()
    {
        $this->load->view(
            'template',
            array(
                'main' => 'ams/standard-series/landing',
                'title' => "AMS Standard Series",
                'subtitle' => "Select an AMS Standard Series element",
            )
        );
    }

    public function index($element = null)
    {
        if ($element === null) {
            return $this->_landing();
        }
        $element = self::_validElementOr404($element);
        $all_series = Doctrine_Query::create()
            ->from($element . self::TABLE_SUFFIX)
            ->orderBy('code ASC')
            ->execute();
        $this->load->view(
            'template',
            array(
                'main' => 'ams/standard-series/index',
                'title' => "$element AMS Standard Series",
                'subtitle' => "Manage $element AMS Standard Series",
                'all_series' => $all_series,
                'element' => $element,
            )
        );
    }

    public function add($element = null, $id = null)
    {
        $this->edit($element, $id);
    }

    public function edit($element = null, $id = null)
    {
        $element = self::_validElementOr404($element);
        $cls = $element . self::TABLE_SUFFIX;
        $series = $id === null ? new $cls() : self::_find($element, $id);
        if ($this->input->post()) {
            $valid = $this->_validate();
            $series = $series->merge($this->input->post('series'));
            if ($valid) {
                $series->save();
                redirect("ams/standard-series/view/$element/$series->id");
            }
        }

        $action = $id !== null ? 'Edit' : 'Add';
        $this->load->view(
            'template',
            array(
                'main' => "ams/standard-series/edit",
                'title' => "$action $element AMS Standard Series",
                'subtitle' => "$element Standard Series: $series->code",
                'series' => $series,
                'element' => $element,
            )
        );
    }

    public function view($element = null, $id = null)
    {
        $element = self::_validElementOr404($element);
        $series = prep_for_output(self::_find($element, $id));
        $this->load->view(
            'template',
            array(
                'main' => 'ams/standard-series/view',
                'title' => 'View AMS Standard Series',
                'subtitle' => "Series: $series->code",
                'series' => $series,
                'element' => $element,
            )
        );
    }

    protected static function _find($element, $id)
    {
        $table_name = $element . self::TABLE_SUFFIX;
        $series = Doctrine_Core::getTable($table_name)->find($id);
        if (!$series) {
            show_404();
        }
        return $series;
    }

    protected function _validate()
    {
        return $this->form_validation->run('AMSStandardSeries');
    }
}

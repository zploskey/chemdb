<?php

class Carriers extends MY_Controller
{

    protected $ELEMENT_NAME = array(
        'al' => 'Aluminum',
        'be' => 'Beryllium',
    );

    /**
     * Loads a page listing carriers of links to the carrier types.
     *
     * @return void
     **/
    function index($element = '', $sort_by = 'id', $sort_dir = 'asc')
    {
        $element = strtolower($element);
        if ($element) {
            if (! in_array($element, array_keys($this->ELEMENT_NAME))) {
                show_404('page');
            }

            $tableName = ucfirst($element) . 'Carrier';

            $carriers = Doctrine_Query::create()
                ->from($tableName)
                ->orderBy("$sort_by $sort_dir")
                ->execute();

            $elementName = $this->ELEMENT_NAME[$element];

            $data = array(
                'title' => "Manage $elementName Carriers",
                'carriers' => $carriers,
                'sort_by' => $sort_by,
                'sort_dir' => $sort_dir,
                'alt_sort_dir' => switch_sort($sort_dir),
                'longname' => $elementName . ' Carrier',
            );
        } else {
            $data = array('title' => 'Manage Carriers');
        }

        $data['main'] = 'carriers/index';
        $data['element'] = $element;

        $this->load->view('template', $data);
    }

    /**
     * Displays the edit form and evaluates submits. If the submit validates properly,
     * it makes change to carrier in database and redirects.
     *
     */
    function edit($element, $id = 0)
    {
        $is_refresh = $this->input->post('is_refresh');

        if (! in_array($element, array_keys($this->ELEMENT_NAME))) {
            show_404('page');
        }

        $query = Doctrine_Query::create()
            ->from($element)
            ->where('id = ?', $id);

        $data = new stdClass();
        $longname = $this->ELEMENT_NAME[$element] . ' Carrier';

        if ($id) {
            // edit an existing carrier
            $carrier = $query->fetchOne();

            if (!$carrier) {
                show_404('page');
            }

            $data->title = "Edit $longname";
            $data->subtitle   = "Editing $longname: $carrier->name";
        } else {
            // create a new carrier object
            $tableName = ucfirst($element) . 'Carrier';
            $carrier = new $tableName;

            $data->title = "Add $longname";
            $data->subtitle = "Enter $longname Information:";
        }

        if ($is_refresh) {
            // validate what was submitted
            $valid = $this->form_validation->run($element);
            $carrier->name = $this->input->post('name');

            if ($valid) {
                $carrier->save();
                $carrier = $query->where('id = ?', $carrier->id)->fetchOne();
            }
        }

        $data->carrier = $carrier;
        $data->element = $element;
        $data->longname = $longname;
        $data->main  = 'carriers/edit';
        $this->load->view('template', $data);
    }

    /**
     * Shows the data for a carrier.
     *
     * @return void
     */
    function view($element, $id)
    {
        if (! in_array($element, array_keys($this->ELEMENT_NAME))) {
            show_404('page');
        }

        $longname = $this->ELEMENT_NAME[$element];
        $carrier = Doctrine_Core::getTable($element)->find($id);

        if ( ! $carrier) {
            show_404('page');
        }
        $namefield = $this->NAMEFIELD_MAP[$element];
        $data = new stdClass();
        $data->title      = 'View Carrier';
        $data->type       = $element;
        $data->subtitle   = "Viewing $longname: $data->number";
        $data->longname   = $longname;
        $data->carrier  = $carrier;
        $data->main       = 'carriers/view';
        $this->load->view('template', $data);
    }

}

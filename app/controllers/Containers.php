<?php

class Containers extends MY_Controller
{

    const LONGNAME_MAP = array(
        'SplitBkr'   => 'Split Beaker',
        'DissBottle' => 'Dissolution Bottle',
    );

    const NAMEFIELD_MAP = array(
        'SplitBkr' => 'bkr_number',
        'DissBottle' => 'bottle_number',
    );

    /**
     * Loads a page listing containers of links to the container types.
     *
     * @return void
     **/
    function index($type = '', $sort_by = 'id', $sort_dir = 'asc')
    {
        if ($type) {
            if (! in_array($type, array_keys(self::LONGNAME_MAP))) {
                show_404('page');
            }

            $namefield = self::NAMEFIELD_MAP[$type];

            if ($sort_by === 'number') {
                $sort_by = $namefield;
            }

            $containers = Doctrine_Query::create()
                ->from($type)
                ->orderBy("$sort_by $sort_dir")
                ->execute();

            $data = array(
                'title' => 'Manage ' . self::LONGNAME_MAP[$type] . 's',
                'containers' => $containers,
                'sort_by' => $sort_by,
                'sort_dir' => $sort_dir,
                'alt_sort_dir' => switch_sort($sort_dir),
                'longname' => self::LONGNAME_MAP[$type],
                'number' => $namefield,
            );
        } else {
            $data = array('title' => 'Manage Containers');
        }

        $data['main'] = 'containers/index';
        $data['type'] = $type;

        $this->load->view('template', $data);
    }

    /**
     * Displays the edit form and evaluates submits. If the submit validates properly,
     * it makes change to container in database and redirects.
     *
     */
    function edit($type, $id = 0)
    {
        $is_refresh = $this->input->post('is_refresh');

        if (! in_array($type, array_keys(self::LONGNAME_MAP))) {
            show_404('page');
        }

        $query = Doctrine_Query::create()
            ->from($type)
            ->where('id = ?', $id);

        $data = new stdClass();
        $namefield = self::NAMEFIELD_MAP[$type];
        $longname = self::LONGNAME_MAP[$type];

        if ($id) {
            // edit an existing container
            $container = $query->fetchOne();

            if (!$container) {
                show_404('page');
            }

            $data->title = "Edit $longname";
            $data->subtitle   = "Editing $longname: " . $container->$namefield;
        } else {
            // create a new container object
            $container = new $type;

            $data->title = "Add $longname";
            $data->subtitle = "Enter $longname Information:";
        }

        if ($is_refresh) {
            // validate what was submitted
            $valid = $this->form_validation->run($type);
            $container->$namefield = $this->input->post($namefield);

            if ($valid) {
                $container->save();
                $container = $query->where('id = ?', $container->id)->fetchOne();
            }
        }

        $data->container = $container;
        $data->number = $namefield;
        $data->type = $type;
        $data->longname = $longname;
        $data->main  = 'containers/edit';
        $this->load->view('template', $data);
    }

    /**
     * Shows the data for a container.
     *
     * @return void
     */
    function view($type, $id)
    {
        if (! in_array($type, array_keys(self::LONGNAME_MAP))) {
            show_404('page');
        }

        $longname = self::LONGNAME_MAP[$type];
        $container = Doctrine::getTable($type)->find($id);

        if ( ! $container) {
            show_404('page');
        }
        $namefield = self::NAMEFIELD_MAP[$type];
        $data = new stdClass();
        $data->title      = 'View Container';
        $data->type       = $type;
        $data->number     = $container->$namefield;
        $data->subtitle   = "Viewing $longname: $data->number";
        $data->longname   = $longname;
        $data->container  = $container;
        $data->main       = 'containers/view';
        $this->load->view('template', $data);
    }

}

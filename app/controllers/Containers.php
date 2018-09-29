<?php

class Containers extends MY_Controller
{
    protected $LONGNAME_MAP = array(
        'SplitBkr'   => 'Split Beaker',
        'DissBottle' => 'Dissolution Bottle',
    );

    protected $NAMEFIELD_MAP = array(
        'SplitBkr'   => 'bkr_number',
        'DissBottle' => 'bottle_number',
    );

    /**
     * Loads a page listing containers of links to the container types.
     *
     * @param string $type Container type
     * @param string $sort_by Container property to sort by
     * @param string $sort_dir Sort direction ('asc' or 'desc')
     **/
    public function index($type = '', $sort_by = 'id', $sort_dir = 'asc')
    {
        if ($type) {
            if (!in_array($type, array_keys($this->LONGNAME_MAP))) {
                show_404('page');
            }

            $namefield = $this->NAMEFIELD_MAP[$type];

            if ($sort_by === 'number') {
                $sort_by = $namefield;
            }

            $containers = Doctrine_Query::create()
                ->from($type)
                ->orderBy("$sort_by $sort_dir")
                ->execute();

            $data = array(
                'title'        => 'Manage ' . $this->LONGNAME_MAP[$type] . 's',
                'containers'   => $containers,
                'sort_by'      => $sort_by,
                'sort_dir'     => $sort_dir,
                'alt_sort_dir' => switch_sort($sort_dir),
                'longname'     => $this->LONGNAME_MAP[$type],
                'number'       => $namefield,
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
     * @param string $type Container type
     * @param int $id Container id
     */
    public function edit($type, $id = 0)
    {
        $is_refresh = $this->input->post('is_refresh');

        if (!in_array($type, array_keys($this->LONGNAME_MAP))) {
            show_404('page');
        }

        $query = Doctrine_Query::create()
            ->from($type)
            ->where('id = ?', $id);

        $data = new stdClass();
        $namefield = $this->NAMEFIELD_MAP[$type];
        $longname = $this->LONGNAME_MAP[$type];

        if ($id) {
            // edit an existing container
            $container = $query->fetchOne();

            if (!$container) {
                show_404('page');
            }
        } else {
            // create a new container object
            $container = new $type;
        }

        if ($is_refresh) {
            // validate what was submitted
            $valid = $this->form_validation->run($type);
            $container->$namefield = $this->input->post($namefield);

            if ($valid) {
                $container->save();
                $id = $container->id;
                redirect("containers/view/$type/$id");
            }
        }

        if ($id) {
            $data->title = "Edit $longname";
            $data->subtitle = "Editing $longname";
        } else {
            $data->title = "Add $longname";
            $data->subtitle = "Enter $longname Information:";
        }

        $container->$namefield = html_escape($container->$namefield);
        $data->container = $container;
        $data->namefield = $namefield;
        $data->type = $type;
        $data->longname = $longname;
        $data->main = 'containers/edit';
        $this->load->view('template', $data);
    }

    /**
     * Shows the data for a container.
     *
     * @param string $type Container type
     * @param int $id Container id
     */
    public function view($type, $id)
    {
        if (!in_array($type, array_keys($this->LONGNAME_MAP))) {
            show_404('page');
        }

        $longname = $this->LONGNAME_MAP[$type];
        $container = Doctrine_Core::getTable($type)->find($id);

        if (!$container) {
            show_404('page');
        }
        $namefield = $this->NAMEFIELD_MAP[$type];
        $data = new stdClass();
        $data->title = 'View Container';
        $data->type = $type;
        $data->number = $container->$namefield;
        $data->subtitle = "Viewing $longname: $data->number";
        $data->longname = $longname;
        $data->container = $container;
        $data->main = 'containers/view';
        $this->load->view('template', $data);
    }
}

<?php

class Projects extends MY_Controller
{

    /**
     * Loads a page listing projects.
     *
     * Uses pagination if there are many projects.
     *
     * @return void
     **/
    function index()
    {
        // Pagination url: projects/index/sort_by/sort_dir/page
        //     URI number:     1   /  2  /   3   /    4   / 5
        //       Defaults: projects/index/  name /   asc  / 0

        // set database pagination settings
        $sort_by = $this->uri->segment(3, 'name');
        $sort_dir = strtolower($this->uri->segment(4, 'ASC'));
        $num_per_page = 20;
        $page = $this->uri->segment(5, 0);
        $projects = Doctrine_Query::create()
            ->from('Project')
            ->orderBy("$sort_by $sort_dir")
            ->limit($num_per_page)
            ->offset($page)
            ->execute();

        $nrows = Doctrine::getTable('Project')->count();
        $alt_sort_page = $nrows - $page - $num_per_page;
        if ($alt_sort_page < 0) {
            $alt_sort_page = 0;
        }

        // set pagination options
        $this->load->library('pagination');
        $config['base_url'] = site_url("projects/index/$sort_by/$sort_dir");
        $config['total_rows'] = $nrows;
        $config['per_page'] = $num_per_page;
        $config['uri_segment'] = 5;
        $this->pagination->initialize($config);

        $data = array(
            'title'        => 'Manage Projects',
            'main'         => 'projects/index',
            'projects'     => $projects,
            'paginate'     => ($nrows > $num_per_page),
            'pagination'   => $this->pagination->create_links(),
            'alt_sort_dir' => switch_sort($sort_dir),  // a little trick I put in the snippet helper
            'sort_by'      => $sort_by,
            'page'         => $page,
            'alt_sort_page' => $alt_sort_page,
        );

        $this->load->view('template', $data);
    }

    /**
     * Displays the edit form and evaluates submits. If the submit validates properly,
     * it makes change to project in database and redirects.
     *
     * @param int id The id of the project to edit. Default is zero for a new project.
     */
    function edit($id = 0)
    {
        $is_refresh = $this->input->post('is_refresh');

        $data = new stdClass();
        // are we editing an existing project?
        if ($id) {
            // Get the project data.
            $proj = Doctrine_Query::create()
                ->from('Project p, p.Sample')
                ->where('p.id = ?', $id)
                ->fetchOne();

            // If the project doesn't exist we 404.
            if ( ! $proj) {
                show_404('page');
            }
            if (isset($proj->Sample)) $data->samples = $proj->Sample;

            // there is a project, set the display values
            $data->title = 'Edit Project';
            $data->subtitle = 'Editing '.$proj->name;
            $data->arg = $id;
        } else {
            // it's a new project, create a new record object and other display values
            $proj = new Project();
            $proj->date_added = date('Y-m-d h:m:s');
            $data->title = 'Add Project';
            $data->subtitle = 'Enter Project Information:';
            $data->arg = '';
        }

        // set up some javascript to add more project select boxes
        $data->extraHeadContent =
            '<script type="text/javascript" src="js/sample_search.js"></script>';

        // validate anything that was submitted
        if ($is_refresh) {
            $valid = $this->form_validation->run('projects');
            $proj->name = $this->input->post('name');
            $proj->description = $this->input->post('description');
            $samp = $this->input->post('samp');

            if ($valid) {
                // inputs are valid, save changes and redirect
                if (!($samp == '')) {
                    $sample = Doctrine_Query::create()
                        ->from('Sample s, s.ProjectSample ps')
                        ->select('s.id')
                        ->where('s.name = ?', $samp)
                        ->fetchOne();

                    if (isset($proj->Sample)) {
                        $n = $proj->Sample->count();
                    } else {
                        $n = 0;
                    }

                    $exists = false;
                    foreach ($sample->ProjectSample->toArray() as $link) {
                        if ($link['project_id'] == $proj->id) {
                            $exists = true;
                            break;
                        }
                    }

                    if ($samp && !$exists) {
                        $proj['ProjectSample'][$n]['project_id'] = $proj->id;
                        $proj['ProjectSample'][$n]['sample_id'] = $sample->id;
                    }
                }
                $proj->save();
                redirect('projects/edit/'.$proj->id);
            }

        }
        $data->proj = $proj;
        $data->main = 'projects/edit';
        $this->load->view('template', $data);
    }

    /**
     * Shows the project information.
     *
     * @return void
     */
    function view($id)
    {
        $proj = Doctrine_Query::create()
            ->from('Project p, p.Sample')
            ->where('p.id = ?', $id)
            ->fetchOne();

        if ( ! $proj) {
            show_404('page');
        }

        $data = new stdClass();
        $data->title = 'View Project';
        $data->subtitle = 'Viewing '.$proj->name;
        $data->arg = '/'.$id;
        $data->proj = $proj;
        $data->main  = 'projects/view';
        $this->load->view('template', $data);
    }

    // ----------
    // CALLBACKS:
    // ----------

    function _sample_exists($val)
    {
        $val = trim($val);
        if ($val == '') {
            return true;
        }

        $sample = Doctrine_Query::create()
            ->from("Sample s")
            ->where('s.name = ?', $val)
            ->fetchOne();

        if ($sample) {
            return true;
        }

        $this->form_validation->set_message('_sample_exists', 'This %s does not exist in the database.');
        return false;
    }

}

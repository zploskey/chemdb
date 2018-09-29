<?php

class Samples extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('Calculator');
    }

    /**
     * Loads a page listing samples.
     **/
    public function index()
    {
        $query = $this->_handle_query_session();
        $paginated_data = $this->_paginate($query);

        $display_data = array(
            'title'            => 'Manage Samples',
            'main'             => 'samples/index',
            'extraHeadContent' => '<script src="js/sample_search.js"></script>',
        );

        $data = array_merge($display_data, $paginated_data);
        $this->load->view('template', $data);
    }

    /**
     * Finds the query string for the current search. New search queries are
     * stored in the user's session so that the user can access different pages
     * of the search results.
     *
     * @return $query the user's query string
     */
    public function _handle_query_session()
    {
        $query = $this->input->post('query');
        $is_continuation = $this->uri->segment(3);

        if ($query) {
            $this->session->set_userdata('sample_query', trim($query));
        } elseif ($is_continuation) {
            $query = $this->session->userdata('sample_query');
        } else {
            $this->session->unset_userdata('sample_query');
            $query = null;
        }
        return $query;
    }

    /**
     *   Pagination url: samples/index/sort_by/sort_dir/page
     *      URI number:    1    /  2  /   3   /   4    / 5
     *        Defaults:  samples/index/  name /  asc   / 0
     *
     * @param string $query
     * @return array $data Pagination settings and sample list
     **/
    protected function _paginate($query)
    {
        $sort_by = $this->uri->segment(3, 'name');
        $sort_dir = strtolower($this->uri->segment(4, 'asc'));
        $page = $this->uri->segment(5, 0);
        $num_per_page = 20;

        $q = Doctrine_Query::create()
            ->from('Sample s')
            ->select('s.id, s.name')
            ->orderBy("$sort_by $sort_dir")
            ->limit($num_per_page)
            ->offset($page);

        if (isset($query)) {
            $q->where('s.name LIKE ?', "%$query%");
            $nrows = Doctrine_Core::getTable('Sample')->countLike($query);
        } else {
            $nrows = Doctrine_Core::getTable('Sample')->count();
        }

        $samples = $q->execute();

        $this->load->library('pagination');
        $config['base_url'] = site_url("samples/index/$sort_by/$sort_dir/");
        $config['total_rows'] = $nrows;
        $config['per_page'] = $num_per_page;
        $config['uri_segment'] = 5;
        $this->pagination->initialize($config);

        $data = array(
            'samples'          => prep_for_output($samples),
            'paginate'         => ($nrows > $num_per_page),
            'pagination'       => 'Go to page: ' . $this->pagination->create_links(),
            'sort_by'          => $sort_by,
            'alt_sort_dir'     => switch_sort($sort_dir),
            'page'             => $page,
            'query'            => $query,
        );

        return $data;
    }

    /**
     * Displays the edit form and evaluates submits. If the submit validates properly,
     * it makes change to sample in database and redirects.
     *
     * @param int $id Sample id
     */
    public function edit($id = 0)
    {
        $is_refresh = $this->input->post('is_refresh');

        $query = Doctrine_Query::create()
            ->from('Sample s, s.Project p')
            ->where('s.id = ?', $id);

        $data = new stdClass();
        if ($id) {
            // edit an existing sample
            $sample = $query->fetchOne();

            if (!$sample) {
                show_404('page');
            }

            if (isset($sample->Project)) {
                $data->project = $sample->Project;
            }

            $data->title = 'Edit Sample';
            $data->subtitle = 'Editing ' . $sample->name;
            $data->arg = $id;
        } else {
            // create a new sample object
            $sample = new Sample();

            $data->title = 'Add Sample';
            $data->subtitle = 'Enter Sample Information:';
            $data->arg = '';
        }

        if ($is_refresh) {
            // validate what was submitted
            $valid = $this->form_validation->run('samples');
            $sample->merge($this->input->post('sample'));
            if (!$this->input->post('antarctic')) {
                $sample->antarctic = false;
            }
            $proj = $this->input->post('proj');

            if ($proj) {
                $proj = array_unique($proj);
            }

            if ($valid) {
                $sample->unlink('Project');
                $sample->save();
                $off = 0;
                for ($i = 0; $i < count($proj); $i++) {
                    if ($proj[$i] == '') {
                        $off++;
                        continue;
                    }
                    $sample['ProjectSample'][$i - $off]['project_id'] = $proj[$i];
                    $sample['ProjectSample'][$i - $off]['sample_id'] = $sample->id;
                }
                $sample->save();
            }
        }

        // generate some select boxes to associate projects with the sample
        $projOptions = array();
        $nprojs = 0;
        if (isset($sample->Project)) {
            $nprojs = $sample->Project->count();
            for ($i = 0; $i < $nprojs; $i++) {
                $projOptions[] = '<option>';
            }
        }
        $projects = Doctrine_Core::getTable('Project')->getList();
        $defaultSelect = '<option></option>';
        foreach ($projects as $p) {
            $defaultOption = "<option value=$p->id>$p->name</option>";
            $selected = "<option value=$p->id selected>$p->name</option>";
            if ($nprojs) {
                for ($i = 0; $i < $nprojs; $i++) {
                    if ($sample['Project'][$i]['id'] == $p['id']) {
                        $projOptions[$i] .= $selected;
                    } else {
                        $projOptions[$i] .= $defaultOption;
                    }
                }
            }
            $defaultSelect .= $defaultOption;
        }
        $data->defaultSelect = $defaultSelect;
        $data->projOptions = $projOptions;

        // set up some javascript to add more project select boxes
        $data->extraHeadContent = <<<EHC
            <script>
                var defaultSelect = "$defaultSelect";
            </script>
            <script src="js/editSample.js"></script>
EHC;

        $data->sample = prep_for_output($sample);
        $data->main = 'samples/edit';
        $this->load->view('template', $data);
    }

    /**
     * Shows the data for a sample and allows the user to calculate exposure ages for
     * a given analysis and AMS measurement.
     *
     * @param int $id Sample id
     */
    public function view($id)
    {
        $sample = Doctrine_Core::getTable('Sample')->fetchViewdataById($id);

        if (!$sample) {
            show_404('page');
        }

        $data = new stdClass();
        if (isset($sample->Project)) {
            $data->projects = $sample->Project;
        }

        $data->nAnalyses = $sample->Analysis->count();

        // callback to convert from g to ug and round to 2 decimal places
        function convAndRound($x)
        {
            return sprintf('%.1f', round($x * 1e6, 1));
        }

        // Calculate our derived data (weights and concentrations)
        $calcsExist = false;
        foreach ($sample->Analysis as $an) {
            // convert and round the ICP weights
            $massAl = array_map('convAndRound', $an->getMassIcp('Al'));
            $massBe = array_map('convAndRound', $an->getMassIcp('Be'));
            // add a +/- sign before the error
            $data->ugAl[] = implode(' &plusmn; ', $massAl);
            $data->ugBe[] = implode(' &plusmn; ', $massBe);
            $ppmAl = safe_divide($massAl[0] * 1e-6, $an->getSampleWt());
            $ppmAl = sprintf('%.3e', $ppmAl);
            // and we'll show in value x 10^(superscript) style
            $data->ppmAl[] = str_replace('e', ' &times; 10<sup>', $ppmAl) . '</sup>';
            $data->yieldBe[] = sprintf('%.3f', $an->getPctYield('Be'));
            if (!$calcsExist) {
                $calcsExist = isset($an->BeAms[0]->BeAmsStd) || isset($an->AlAms[0]->AlAmsStd);
            }
        }

        $data->calcsExist = $calcsExist;
        $data->title = 'View Sample';
        $data->subtitle = 'Viewing ' . $sample->name;
        $data->sample = prep_for_output($sample);
        $data->main = 'samples/view';
        $this->load->view('template', $data);
    }

    /**
     *  Sends a request to the CRONUS calculator for dating and displays the results
     *  in a new window.
     * @param int $id Sample id
     */
    public function submit_to_calc($id)
    {
        $sample = Doctrine_Core::getTable('Sample')->fetchViewdataById($id);
        $nAnalyses = $sample->Analysis->count();
        $useIcpBe = array();
        for ($i = 0; $i < $nAnalyses; $i++) {
            $useIcpBe[$i] = ($this->input->post('analysis' . $i) == 'icp');
        }
        $calcInputs = $sample->getCalcInputs($useIcpBe);

        // find out what the user wanted
        $calcSel = true;
        if ($this->input->post('calcSelEro')) {
            $calcType = 'erosion';
        } elseif ($this->input->post('calcSelAge')) {
            $calcType = 'age';
        } else {
            $calcSel = false;
            for ($i = 0; $i < $sample->Analysis->count(); $i++) {
                if ($this->input->post('calcAge_' . $i)) {
                    $calcType = 'age';
                    $nToCalc = $i;
                    break;
                }
                if ($this->input->post('calcEro_' . $i)) {
                    $calcType = 'erosion';
                    $nToCalc = $i;
                    break;
                }
            }
            $incInReport = array($nToCalc);
        }

        if ($calcSel) {
            $incInReport = $this->input->post('incInReport');
            if (!is_array($incInReport)) {
                die('You must select at least one analysis to send to the calculator.');
            }
            if (count($incInReport) == 0) {
                die('You must select at least one analysis to submit.');
            }
        }

        if (!isset($calcType)) {
            die('Error: Calculation type was not specified.');
        }

        foreach ($incInReport as $i) {
            $tmp[] = implode("\n", $calcInputs[$calcType][$i]);
        }
        $submitText = implode("\n", $tmp);

        $html = $this->calculator->send($submitText, $calcType);
        echo $html;
    }

    /**
     * A callback function for javascript auto-completion of sample names.
     * Prints all the names that contain the query ($_POST['q']), each
     * followed by a newline character.
     */
    public function search_names()
    {
        $q = $this->input->get('term');

        if (!$q) {
            return;
        }

        $samples = Doctrine_Query::create()
               ->from('Sample s')
               ->select('s.name')
               ->where('s.name LIKE ?', "%$q%")
               ->orderBy('s.name ASC')
               ->fetchArray();

        if (!$samples) {
            return;
        }

        $res = array();
        foreach ($samples as $s) {
            $res[] = $s['name'];
        }

        echo json_encode($res);
    }

    /**
     * Validates the antarctic checkbox. Returns true if the value is 1.
     *
     * @param string $val Checkbox value
     * @return bool
     **/
    public function _valid_antarctic($val)
    {
        if (!isset($val) || $val == 1) {
            return true;
        }
        $this->form_validation->set_message(
            'sample[antarctic]',
            'The %s field must be checked (set to 1) or not selected at all.'
        );
        return false;
    }
}

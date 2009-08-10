<?php

class Samples extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    /**
     * Loads a page listing samples.
     *
     * @return void
     **/
    function index()
    {
        $query = $this->_handle_query_session();
        $paginated_data = $this->_paginate($query);

        $display_data = array(
            'title'            => 'Manage Samples',
            'main'             => 'samples/index',
            'extraHeadContent' => 
                '<script type="text/javascript" src="js/sample_search.js"></script>',
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
    function _handle_query_session()
    {
        $query = $this->input->post('query');
        $is_continuation = $this->uri->segment(3);

        if ($query !== false) {
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
     *      URI number:     1   /  2  /   3   /    4   / 5
     *        Defaults: samples/index /  name  /  desc  / 0
     *
     * @return $data array with pagination settings and sample list included
     **/
    function _paginate($query)
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
            $nrows = Doctrine::getTable('Sample')->countLike($query);
        } else {
            $nrows = Doctrine::getTable('Sample')->count();
        }

        $samples = $q->execute();

        $this->load->library('pagination');
        $config['base_url'] = site_url("samples/index/$sort_by/$sort_dir/");
        $config['total_rows'] = $nrows;
        $config['per_page'] = $num_per_page;
        $config['uri_segment'] = 5;
        $this->pagination->initialize($config);

        $data = array(
            'samples'          => $samples,
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
     */
    function edit($id = 0) 
    {
        $is_refresh = $this->input->post('is_refresh');

        if ($id) {
            // edit an existing sample
            $sample = Doctrine_Query::create()
                ->from('Sample s, s.Project p')
                ->where('s.id = ?', $id)
                ->fetchOne();

            if (!$sample) {
                show_404('page');
            }

            if (isset($sample->Project)) $data->project = $sample->Project;

            $data->title = 'Edit Sample';
            $data->subtitle = 'Editing '.$sample->name;
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
                $sample->merge($this->input->post('sample'));
                $sample->unlink('Project');
                $sample->save();
                $off = 0;
                for ($i = 0; $i < count($proj); $i++) {
                    if ($proj[$i] == '') {
                        $off++;
                        continue;
                    }
                    $sample['ProjectSample'][$i-$off]['project_id'] = $proj[$i];
                    $sample['ProjectSample'][$i-$off]['sample_id'] = $sample->id; 
                }
                $sample->save();
                $sample = Doctrine_Query::create()
                    ->from('Sample s, s.Project p')
                    ->where('s.id = ?', $sample->id)
                    ->fetchOne();
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
        $projects = Doctrine::getTable('Project')->getList();
        $defaultSelect = '<option>';
        foreach ($projects as $p) {
            $defaultOption = "<option value=$p->id>$p->name";
            $selected = "<option value=$p->id selected>$p->name";
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
            <script type="text/javascript">
            var options='<select name="proj[]">$defaultSelect</select><br/>';
            $(document).ready(function() {
                $("#add_select").click(function(event) {
                    event.preventDefault();
                    $(options).insertBefore("#add_select");
                });
            });
            </script>
EHC;

        $data->sample = $sample;
        $data->main = 'samples/edit';
        $this->load->view('template', $data);
    }

    /**
     * Shows the data for a sample.
     *
     * @return void
     */
    function view($id)
    {
        $sample = Doctrine_Query::create()
            ->from('Sample s')
            ->leftJoin('s.Project p')
            ->leftJoin('s.Analysis a')
            ->leftJoin('a.Batch b')
            ->leftJoin('b.BeCarrier bec')
            ->leftJoin('b.AlCarrier alc')
            ->leftJoin('b.Analysis bana')
            ->leftJoin('a.BeAms ba')
            ->leftJoin('ba.BeAmsStd bas')
            ->leftJoin('bas.BeStdSeries ss')
            ->orderBy('a.id ASC')
            ->addOrderBy('ba.date DESC')
            ->where('s.id = ?', $id)
            ->fetchOne();
            
        if ( ! $sample) {
            show_404('page');
        }
        
        if (isset($sample->Project)) $data->projects = $sample->Project;
        $an_text = array();
        foreach ($sample->Analysis as $an) {
            foreach($an->BeAms as $ams) {
                if (!isset($ams->BeAmsStd) || !isset($ams->BeAmsStd->BeStdSeries)) {
                    // we don't have a standard set, no sense in even showing it
                    continue;
                }

                if ($sample->antarctic) {
                    $pressure_flag = 'ant';
                } else {
                    $pressure_flag = 'std';
                }
                
                $bec = $an->Batch->BeCarrier;
                $alc = $an->Batch->AlCarrier;
                
                // calculate Be10 concentration and its error
                // these calculations are based on:
                // Converting Al and Be isotope ratio measurements to nuclide 
                // concentrations in quartz.
                // Greg Balco
                // May 8, 2006
                // http://hess.ess.washington.edu/math/docs/common/ams_data_reduction.pdf
                
                // first find our blank
                foreach ($an->Batch->Analysis as $tmpa) {
                    if ($analysis->sample_type == 'BLANK') {
                        $blank = $tmpa;
                        break;
                    }
                }
                // we now need to estimate the Be10 concentration of the blank if found
                if (isset($blank)) {
                    $r10to9_b = $blank->BeAms->r_to_rstd * $blank->BeAms->BeAmsStd->r10to9;
                    $M_c_b = $blank->wt_be_carrier * $bec->be_conc * 1e-6;
                    $M_b = $r10to9_b * $M_c_b * AVOGADRO / MM_BE;
                } else {
                    $M_b = 0;
                }
                
                // mass of Be in the sample initially
                $M_qtz = $an->wt_diss_bottle_tare - $an->wt_diss_bottle_sample;
                // Be10/Be9 ratio of the sample
                $R_10to9 = $ams->r_to_rstd * $ams->BeAmsStd->r10to9;
                // mass of Be added as carrier (grams)
                // concentration is converted from ug
                $M_c = $an->wt_be_carrier * 1e-6 * $bec->be_conc;
                $M_c_err = $bec->del_be_conc * 1e-6 * $M_c;
                // estimate of Be10 concentration of sample
                $be10_conc = (1 / $M_qtz) * ($r10to9 * $M_c * AVOGADRO / MM_BE - $M_b);
                
                // Calculate the error in Be10 concentration ($be10_conc):
                // First, define the differentials for each error source. Each is
                // equivalent to del(Number of Be10 atoms)/del(source variable)
                // multiplied by the error in the source variable.
                $err_terms = array(
                    $ams->exterror * $M_c * AVOGADRO / $M_qtz * MM_BE, // from ams error
                    -1 / $M_qtz, // from blank error
                    $ams->r10to9 * AVOGADRO / $M_q * MM_BE, // from carrier error
                );
                $be10_conc_err = sqrt(sum_of_squares($err_terms));
                
                // we default to a zero aluminum analysis
                if (!isset($sample->Analysis->AlAms)) {
                    $al26_conc = $al26_err = 0;
                    $al26_code = 'KNSTD';
                } else {
                    // calculate aluminum concentration and error
                    // temporary settings until we work out this calculation
                    $al26_conc = $al26_err = 0;
                    $al26_code = 'KNSTD';
                }

                $entries = array(
                    substr($sample->name, 0, 24),
                    $sample->latitude,
                    $sample->longitude,
                    $sample->altitude,
                    $pressure_flag,
                    abs($sample->depth_bottom - $sample->depth_top),
                    $sample->density,
                    $sample->shield_factor,
                    $be10_conc,
                    $ams->exterror, // Be10 uncertainty from AMS
                    $ams->BeAmsStd->BeStdCode->code,
                    $al26_conc,
                    $al26_err,
                    $al26_code,
                );
                
                foreach ($entries as $ent) {
                    $text .= ' ' . $ent;
                }
                $text = substr($text, 1);
                $an_text[] = $text;
                
                die($text);
            }
        }
        $data->an_text = $an_text;
        $data->title = 'View Sample';
        $data->subtitle = 'Viewing '.$sample->name;
        $data->arg = $id;
        $data->sample  = $sample;
        $data->main  = 'samples/view';
        $this->load->view('template', $data);
    }

    /**
     * A callback function for javascript auto-completion of sample names.
     * Prints all the names that contain the query ($_POST['q']), each
     * followed by a newline character.
     * @return void
     */
    function search_names()
    {
        $q = $this->input->post('q');

        if (!$q) return;

        $samples = Doctrine_Query::create()
               ->from('Sample s')
               ->select('s.name')
               ->where('s.name LIKE ?', "%$q%")
               ->orderBy('s.name ASC')
               ->execute();

        if (!$samples) return;

        foreach ($samples as $s) {
            echo "$s->name\n";
        }
    }
    
    /**
     * Validates the antarctic checkbox. Returns true if the value is 1.
     *
     * @return bool
     **/
    function _valid_antarctic($val)
    {
        if (!isset($val) || $val == 1) {
            return true;
        }
        $this->form_validation->set_message('sample[antarctic]', 'The %s field must be set to 1.');
        return false;
    }

}

/* End of file samples.php */
/* Location: ./system/application/controllers/samples.php */
<?php

class Alchecks extends MY_Controller
{
    public function index()
    {
        // generate html for the batch listboxes
        $data = new stdClass();
        $data->allBatchOptions = '';
        foreach (Doctrine_Core::getTable('AlcheckBatch')->findAllBatches() as $b) {
            $tmpOpt = "<option value=$b->id>$b->id $b->owner $b->prep_date "
                    .substr($b->description, 0, 80);
            $data->allBatchOptions .= $tmpOpt;
        }

        $data->title = 'Al check options';
        $data->subtitle = 'Aluminum check options';
        $data->main = 'alchecks/index';
        $this->load->view('template', $data);
    }

    public function new_batch()
    {
        $batch_id = (int)$this->input->post('batch_id');
        $is_edit = (bool)$batch_id;
        $refresh = (bool)$this->input->post('refresh');
        $data = new stdClass();
        $data->allow_num_edit = (!$is_edit);

        if ($is_edit) {
            // batch exists, find it
            $batch = Doctrine_Core::getTable('AlcheckBatch')->find($batch_id);
            if (!$batch) {
                show_404('page');
            }
            $nsamples = $batch->AlcheckAnalysis->count();
        } else {
            // create a temporary batch object
            $batch = new AlcheckBatch();
            $nsamples = null;
        }

        $data->errors = false;
        if ($refresh) {
            // validate input
            $is_valid = $this->form_validation->run('al_new_batch');
            // merge our batch with the postdata
            $batch->prep_date = $this->input->post('prep_date');
            $batch->description = $this->input->post('description');
            $batch->owner = $this->input->post('owner');
            $nsamples = $this->input->post('numsamples');

            if ($is_valid) {
                for ($i = 1; $i <= $nsamples; $i++) {
                    $tmp = new AlcheckAnalysis();
                    $tmp->number_within_batch = $i;
                    $batch->AlcheckAnalysis[] = $tmp;
                }
                $batch->save();
                $batch_id = $batch->id;
                $data->allow_num_edit = false;
            } else {
                // we had errors, display them in the view
                $data->errors = true;
                if (strlen(form_error('numsamples')) > 0) {
                    // numsamples had an error, better make sure it's editable
                    $data->allow_num_edit = true;
                }
            }
        }

        $data->batch = $batch;
        $data->batch_id = $batch_id;
        $data->nsamples = $nsamples;
        $data->title = 'Start new Al check batch';
        $data->main = 'alchecks/new_batch';
        $this->load->view('template', $data);
    }

    public function sample_loading()
    {
        $batch_id = (int)$this->input->post('batch_id');
        $refresh = (bool)$this->input->post('refresh');
        $add = isset($_POST['add']);

        $batch = Doctrine_Core::getTable('AlcheckBatch')
               ->getJoinQuery($batch_id)->fetchOne();

        if (!$batch) {
            show_404('page');
        }

        if (isset($batch->AlcheckAnalysis)) {
            $nsamples = $batch->AlcheckAnalysis->count();
        } else {
            $nsamples = 0;
        }

        $data = new stdClass();
        $data->errors = false;
        if ($add) {
            // add a sample to this batch and redirect
            $newAnalysis = new AlcheckAnalysis();
            $newAnalysis['number_within_batch'] = $nsamples + 1;
            $batch->AlcheckAnalysis[] = $newAnalysis;
            $batch->save();
            ++$nsamples;
            $refresh = false;
        }

        if ($refresh) {
            // validate
            $is_valid = $this->form_validation->run('al_sample_loading');

            // grab postdata
            $sample_name = $this->input->post('sample_name');
            $bkr_number = $this->input->post('bkr_number');
            $wt_bkr_tare = $this->input->post('wt_bkr_tare');
            $wt_bkr_sample = $this->input->post('wt_bkr_sample');
            $notes = $this->input->post('notes');

            // update the batch object with our submitted data
            for ($i = 0; $i < $nsamples; $i++) {
                $a = &$batch->AlcheckAnalysis[$i];
                $a['sample_name'] = $sample_name[$i];
                $a['bkr_number'] = $bkr_number[$i];
                $a['wt_bkr_tare'] = $wt_bkr_tare[$i];
                $a['wt_bkr_sample'] = $wt_bkr_sample[$i];
                $a['notes'] = $notes[$i];
            }
            unset($a);

            if ($is_valid) {
                // find samples with same name then:
                // set them as the sample for that analysis
                foreach ($batch->AlcheckAnalysis as &$a) {
                    $sample = Doctrine_Core::getTable('Sample')
                            ->createQuery('s')
                            ->select('s.id, s.name')
                            ->where('s.name = ?', $a['sample_name'])
                            ->fetchOne();

                    if ($sample) {
                        $a['Sample'] = $sample;
                    } else {
                        $a['Sample'] = null;
                    }
                }
                unset($a);
                $batch->save(); // commit it to the database
            } else {
                $data->errors = true;
            }
        } else {
            // loading for the first view
            $sample_name = array();
            foreach ($batch['AlcheckAnalysis'] as $a) {
                if (isset($a->Sample)) {
                    $sample_name[] = $a['Sample']['name'];
                } else {
                    $sample_name[] = $a->sample_name;
                }
            }
        }

        // calculate sample weights
        $data->wt_sample = array();
        foreach ($batch->AlcheckAnalysis as $a) {
            $data->wt_sample[] = $a['wt_bkr_sample'] - $a['wt_bkr_tare'];
        }

        // for autocomplete to work we need to load the script
        $data->extraHeadContent =
            '<script type="text/javascript" src="js/sample_search.js"></script>';
        $data->sample_name = $sample_name;
        $data->nsamples = $nsamples;
        $data->batch = $batch;
        $data->title = 'Stage 1 of new Al check batch';
        $data->main = 'alchecks/sample_loading';
        $this->load->view('template', $data);
    }

    public function add_solution_weights()
    {
        $batch_id = (int)$this->input->post('batch_id');
        $refresh = (bool)$this->input->post('refresh');

        $batch = Doctrine_Core::getTable('AlcheckBatch')
            ->getJoinQuery($batch_id)->fetchOne();

        if (!$batch) {
            show_404('page');
        }

        $data = new stdClass();
        $data->errors = false;
        $nsamples = $batch->AlcheckAnalysis->count();
        if ($refresh) {
            $valid = $this->form_validation->run('al_add_solution_weights');
            $wt_bkr_soln = $this->input->post('wt_bkr_soln');
            $addl_dil_factor = $this->input->post('addl_dil_factor');
            $notes = $this->input->post('notes');
            for ($a = 0; $a < $nsamples; $a++) {
                $batch['AlcheckAnalysis'][$a]['wt_bkr_soln'] = $wt_bkr_soln[$a];
                $batch['AlcheckAnalysis'][$a]['addl_dil_factor'] = $addl_dil_factor[$a];
                $batch['AlcheckAnalysis'][$a]['notes'] = $notes[$a];
            }

            if ($valid) {
                $batch->save();
            } else {
                $data->errors = true;
            }
        }

        $sample_wt = $soln_wt = $tot_df = $sample_name = array();
        for ($i = 0; $i < $nsamples; $i++) {
            $a = $batch['AlcheckAnalysis'][$i];
            $sample_wt[] = $a['wt_bkr_sample'] - $a['wt_bkr_tare'];
            $soln_wt[] = $a['wt_bkr_soln'] - $a['wt_bkr_tare'];
            $tot_df[] = $a['addl_dil_factor'] * safe_divide($soln_wt[$i], $sample_wt[$i]);
            $sample_name[] = (isset($a['Sample'])) ? $a['Sample']['name'] : $a['sample_name'];
        }
        $data->sample_name = $sample_name;
        $data->sample_wt = $sample_wt;
        $data->soln_wt = $soln_wt;
        $data->tot_df = $tot_df;
        $data->nsamples = $nsamples;
        $data->batch = $batch;
        $data->title = 'Add ICP weights to existing batch';
        $data->main = 'alchecks/add_solution_weights';
        $this->load->view('template', $data);
    }

    public function add_icp_data()
    {
        $batch_id = (int)$this->input->post('batch_id');
        $refresh = (bool)$this->input->post('refresh');

        $batch = Doctrine_Core::getTable('AlcheckBatch')
            ->getJoinQuery($batch_id)->fetchOne();

        if (!$batch) {
            show_404('page');
        }

        $elements = array('be', 'al', 'ca', 'fe', 'ti', 'mg', 'k');

        $data = new stdClass();
        $data->errors = false;
        $nsamples = $batch->AlcheckAnalysis->count();
        if ($refresh) {
            $valid = $this->form_validation->run('al_add_icp_data');

            foreach ($elements as $el) {
                ${"icp_$el"} = $this->input->post("icp_$el");
            }

            $notes = $this->input->post('notes');
            $batch['icp_date'] = $this->input->post('icp_date');

            $i = 0;
            foreach ($batch['AlcheckAnalysis'] as &$a) {
                foreach ($elements as $el) {
                    $a["icp_$el"] = ${"icp_$el"}[$i];
                }
                $a['notes'] = $notes[$i];
                ++$i;
            }
            unset($a);

            if ($valid) {
                $batch->save();
            } else {
                $data->errors = true;
            }
        }

        // calculate quartz weights and set sample names
        $sample_name = array();
        for ($i = 0; $i < $nsamples; $i++) {
            $a = $batch['AlcheckAnalysis'][$i];
            // temporary variable calculations
            $sample_wt = $a['wt_bkr_sample'] - $a['wt_bkr_tare'];
            $soln_wt = $a['wt_bkr_soln'] - $a['wt_bkr_tare'];
            $df = $a['addl_dil_factor'] * safe_divide($soln_wt, $sample_wt);
            // variables to pass
            foreach ($elements as $el) {
                $data->{"qtz_$el"}[] = $df * $a["icp_$el"];
            }
            $data->sample_name[] = (isset($a['Sample'])) ? $a['Sample']['name'] : $a['sample_name'];
        }

        if ($batch['icp_date'] === null) {
            $batch['icp_date'] = date('Y-m-d');
        }

        $data->nsamples = $nsamples;
        $data->batch = $batch;
        $data->title = 'Add Be/Al/Ca/Fe/Ti/Mg/K concentrations';
        $data->main = 'alchecks/add_icp_data';
        $this->load->view('template', $data);
    }

    public function report()
    {
        $batch_id = (int)$this->input->post('batch_id');

        $batch = Doctrine_Core::getTable('AlcheckBatch')
            ->getJoinQuery($batch_id)->fetchOne();

        if (!$batch) {
            show_404('page');
        }

        $elements = array('be', 'ca', 'ti', 'fe', 'al', 'mg');
        $nsamples = $batch->AlcheckAnalysis->count();

        // calculate quartz weights and set sample names
        $sample_name = $sample_wt = array();

        for ($i = 0; $i < $nsamples; $i++) {
            $a = $batch['AlcheckAnalysis'][$i];
            // temporary variable calculations
            $sample_wt[] = $a['wt_bkr_sample'] - $a['wt_bkr_tare'];
            $soln_wt = $a['wt_bkr_soln'] - $a['wt_bkr_tare'];
            $df = $a['addl_dil_factor'] * safe_divide($soln_wt, $sample_wt[$i]);
            // variables to pass
            foreach ($elements as $el) {
                $data->{"qtz_$el"}[] = $df * $a["icp_$el"];
            }
            $sample_name[] = (isset($a['Sample'])) ? $a['Sample']['name'] : $a['sample_name'];
        }

        for ($a = 0; $a < $nsamples; $a++) {
            //figure out qtz Al concentration
            if ($data->qtz_al[$a] > 250) {
                $color = 'red';
            } elseif ($data->qtz_al[$a] > 150) {
                $color = 'yellow';
            } else {
                $color = 'green';
            }
            $data->color[] = $color;
        }

        $data->nsamples = $nsamples;
        $data->sample_name = $sample_name;
        $data->sample_wt = $sample_wt;
        $data->batch = $batch;
        if (isset($_SERVER['REMOTE_USER'])) {
            $user = htmlentities($_SERVER['REMOTE_USER']);
        } else {
            $user = '';
        }
        $data->user = $user;
        $this->load->view('alchecks/report', $data);
    }

    public function quick_add()
    {
        $batch_id = $this->input->post('batch_id');
        $analysis_id = $this->input->post('analysis_id');
        $refresh = $this->input->post('refresh');
        $close = $this->input->post('close');

        $an = new AlcheckAnalysis;

        $data = new stdClass();
        $data->errors = true;
        if ($refresh || $close) {
            $valid = $this->form_validation->run('al_quick_add');
            // get postdata
            $an->sample_name = $this->input->post('sample_name');
            $an->icp_al = $this->input->post('icp_al');
            $an->icp_fe = $this->input->post('icp_fe');
            $an->icp_ti = $this->input->post('icp_ti');
            // and set some some default values for our dummy analysis
            $an->number_within_batch = 1;
            $an->bkr_number = '0';
            $an->wt_bkr_tare = 99;
            $an->wt_bkr_sample = 100;
            $an->wt_bkr_soln = 100;

            $samples = Doctrine_Core::getTable('Sample')->findByName($an->sample_name);
            $num_named = 0;
            for ($i = 0; $i < $samples->count(); $i++) {
                if ($samples[$i]->name != '') {
                    $num_named++;
                }
            }

            if ($num_named != 0) {
                if ($num_named > 1) {
                    die('Error: Multiple samples named '.$an->sample_name);
                }
                // Found a single sample
                $an->Sample = $samples[0];
            }

            if (!$batch_id) {
                // create the dummy batch
                $batch = new AlcheckBatch;
                $batch->owner = 'NONE';
                $batch->description = 'Dummy batch';
                $batch->prep_date = date('Y-m-d');
            } else {
                $batch = Doctrine_Core::getTable('AlcheckBatch')
                    ->createQuery('b')
                    ->where('b.id = ?', $batch_id)
                    ->leftJoin('b.AlcheckAnalysis a')
                    ->fetchOne();
                if (!$batch) {
                    die('Batch not found.');
                }
            }

            if ($analysis_id) {
                $n = $batch->AlcheckAnalysis->count();
                $an->id = $batch->AlcheckAnalysis->end()->id;
                $batch->AlcheckAnalysis[$n - 1] = $an;
            } else {
                $batch->AlcheckAnalysis[] = $an;
            }

            if ($valid) {
                $batch->save();
                $batch_id = $batch->id;
                $analysis_id = $batch->AlcheckAnalysis->end()->id;
            } else {
                $data->errors = true;
            }

            if ($close) {
                // they clicked done, close the window
                echo '<script>window.close();</script>';
            }
        }

        $data->analysis = $an;
        $data->batch_id = $batch_id;
        $data->main = 'alchecks/quick_add';
        $data->title = 'Dummy Al check';
        $this->load->view('template', $data);
    }
}

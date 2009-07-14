<?php

class Alchecks extends MY_Controller
{

    function index()
    {
        // generate html for the batch listboxes
        $nBatch = 0;
        foreach (Doctrine::GetTable('AlcheckBatch')->findAllBatches() as $b) {
            $tmpOpt = "<option value=$b->id>$b->prep_date $b->owner $b->description";
            if ($nBatch < 10) {
                $data->recentBatchOptions .= $tmpOpt;
            }
            $data->allBatchOptions .= $tmpOpt;
            $nBatch++;
        }
        
        $data->title = 'Al check options';
        $data->subtitle = 'Aluminum check options';
        $data->main = 'alchecks/index';
        $this->load->view('template', $data);
    }
    
    function new_batch()
    {
        $batch_id = (int)$this->input->post('batch_id');
        $is_edit = (bool)$batch_id;
        $refresh = (bool)$this->input->post('refresh');
        $data->allow_num_edit = ( ! $is_edit);
        
        if ($is_edit) {
            // batch exits, find it
            $batch = Doctrine::GetTable('AlcheckBatch')->find($batch_id);
            if ( ! $batch) {
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
    
    function sample_loading()
    {
        $batch_id = (int)$this->input->post('batch_id');
        $refresh = (bool)$this->input->post('refresh');
        $add = isset($_POST['add']);
        
        $batch = Doctrine::getTable('AlcheckBatch')
               ->getJoinQuery($batch_id)->fetchOne();

        if (! $batch) {
            show_404('page');
        }
        
        if (isset($batch->AlcheckAnalysis)) {
            $nsamples = $batch->AlcheckAnalysis->count();
        } else {
            $nsamples = 0;
        }
        
        if ($add) {
            // add a sample to this batch and redirect
            $newAnalysis = new AlcheckAnalysis();
            $newAnalysis['number_within_batch'] = $nsamples + 1;
            $batch->AlcheckAnalysis[] = $newAnalysis;
            $batch->save();
            // $batch->refreshRelated();
            ++$nsamples;
            $refresh = false;
        }
        
        $data->errors = false;
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

            if ($is_valid) {
                // find samples with same name then:
                // set them as the sample for that analysis
                foreach ($batch->AlcheckAnalysis as &$a) {
                    $sample = Doctrine::getTable('Sample')
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
                $batch->save(); // commit it to the database
            } else {
                $data->errors = true;
            }
            
        } else {
            // loading for the first view
            $sample_name = array();
            foreach ($batch['AlcheckAnalysis'] as $a) {
                if (isset($a['Sample'])) {
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
        $data->sample_name = $sample_name;
        $data->nsamples = $nsamples;
        $data->batch = $batch;
        $data->title = 'Stage 1 of new Al check batch';
        $data->main = 'alchecks/sample_loading';
        $this->load->view('template', $data);
    }
    
    function add_solution_weights()
    {
        $batch_id = (int)$this->input->post('batch_id');
        $refresh = (bool)$this->input->post('refresh');

        $batch = Doctrine::getTable('AlcheckBatch')
            ->getJoinQuery($batch_id)->fetchOne();
        
        if (! $batch) {
            show_404('page');
        }
        
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
    
    function add_icp_data()
    {
        $batch_id = (int)$this->input->post('batch_id');
        $refresh = (bool)$this->input->post('refresh');
        
        $batch = Doctrine::getTable('AlcheckBatch')
            ->getJoinQuery($batch_id)->fetchOne();
            
        if (! $batch) {
            show_404('page');
        }
        
        $elements = array('be', 'ti', 'fe', 'al', 'mg');
        
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
        
        if ($batch['icp_date'] === NULL) {
            $batch['icp_date'] = date('Y-m-d');
        }
        
        $data->nsamples = $nsamples;
        $data->batch = $batch;
        $data->title = 'Add Al/Be/Fe/Ti/Mg concentrations';
        $data->main = 'alchecks/add_icp_data';
        $this->load->view('template', $data);
    }
    
    // CALLBACKS

    /**
     * @param string $date date in YYYY-MM-DD format
     */
    function _valid_date($date) 
    {
        if ($this->form_validation->valid_date($date)) {
            return true;
        }

        $this->form_validation->set_message('_valid_date',
            'The %s field must be a valid date in the format YYYY-MM-DD.');
        return false;
    }
    
}
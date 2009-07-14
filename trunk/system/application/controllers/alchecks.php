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
            $numsamples = $batch->Analysis->count();
        } else {
            // create a temporary batch object
            $batch = new AlcheckBatch();
            $numsamples = null;
        }
        
        $data->errors = false;
        if ($refresh) {
            // validate input
            $is_valid = $this->form_validation->run('al_new_batch');
            // merge our batch with the postdata
            $batch->prep_date = $this->input->post('prep_date');
            $batch->description = $this->input->post('description');
            $batch->owner = $this->input->post('owner');
            $numsamples = $this->input->post('numsamples');
            
            if ($is_valid) {
                for ($i = 1; $i <= $numsamples; $i++) {
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
        $data->numsamples = $numsamples;
        $data->title = 'Start new Al check batch';
        $data->main = 'alchecks/new_batch';
        $this->load->view('template', $data);
    }
    
    function sample_loading()
    {
        $batch_id = (int)$this->input->post('batch_id');
        $refresh = (bool)$this->input->post('refresh');
        $add = isset($_POST['add']);
        
        $batch = Doctrine_Query::create()
            ->from('AlcheckBatch b')
            ->leftJoin('b.AlcheckAnalysis a')
            ->leftJoin('a.Sample s')
            ->select('b.*, a.*, s.id, s.name')
            ->where('b.id = ?', $batch_id)
            ->orderBy('a.number_within_batch ASC')
            ->fetchOne();

        if (! $batch) {
            show_404('page');
        }
        
        if (isset($batch->AlcheckAnalysis)) {
            $numsamples = $batch->AlcheckAnalysis->count();
        } else {
            $numsamples = 0;
        }
        
        if ($add) {
            // add a sample to this batch and redirect
            $newAnalysis = new AlcheckAnalysis();
            $newAnalysis['number_within_batch'] = $numsamples + 1;
            $batch->AlcheckAnalysis[] = $newAnalysis;
            $batch->save();
            // $batch->refreshRelated();
            ++$numsamples;
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
            for ($i = 0; $i < $numsamples; $i++) {
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
        $data->numsamples = $numsamples;
        $data->batch = $batch;
        $data->title = 'Stage 1 of new Al check batch';
        $data->main = 'alchecks/sample_loading';
        $this->load->view('template', $data);
    }
}
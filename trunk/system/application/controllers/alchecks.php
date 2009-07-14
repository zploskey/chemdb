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
        $data->allow_num_edit = ( ! $is_edit);
        
        if ($is_edit) {
            // batch exits, find it
            $batch = Doctrine::GetTable('AlcheckBatch')->find($id);
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
        if ($is_edit) {
            // validate input
            $is_valid = $this->form_validation->run('al_new_batch');
            // merge our batch with the postdata
            $batch->merge($this->input->post('batch'));
            if ($is_valid) {
                $conn = Doctrine_Manager::connection();
                $conn->beginTransaction();
                $batch->save();
                for ($i = 1; $i <= $numsamples; $i++) {
                    $tmp = new AlcheckAnalysis();
                    $tmp->num_within_batch = $i;
                    $tmp->batch_id = $batch->id;
                    $tmp->save();
                }
                $conn->close();
                $batch_id = $batch->id;
            } else {
                // we had errors, display them in the view
                $data->errors = true;
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
        $new = (bool)$this->input->post('new');
        
        $batch = Doctrine_Query::create()
            ->from('AlcheckBatch b')
            ->leftJoin('b.AlcheckAnalysis a')
            ->leftJoin('a.Sample s')
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
        $data->errors = false;
        if ($refresh) {
            if ($new) {
                // add a sample to this batch and redirect
                $batch->addAnalysis();
                $_POST['new'] = false;
                $_POST['refresh'] = false;
                redirect('alcheck/sample_loading');
            }
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
                $a->sample_name = $sample_name[$i];
                $a->bkr_number = $bkr_number[$i];
                $a->wt_bkr_tare = $wt_bkr_tare[$i];
                $a->wt_bkr_sample = $wt_bkr_sample[$i];
                $a->notes = $notes[$i];
            }

            if ($is_valid) {
                $batch->save(); // commit it to the database
            } else {
                $data->errors = true;
            }
            
        }
        
        // calculate sample weights
        $data->wt_sample = array();
        foreach ($batch->AlcheckAnalysis as $a) {
           $data->wt_sample[] = $a->wt_bkr_sample - $a->wt_bkr_tare;
        }
        $data->numsamples = $numsamples;
        $data->batch = $batch;
        $data->title = 'Stage 1 of new Al check batch';
        $data->main = 'alchecks/sample_loading';
        $this->load->view('template', $data);
    }
}
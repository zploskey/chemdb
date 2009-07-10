<?php

class Alchecks extends MY_Controller
{
    var $alAnalysis;
    var $alBatch;
    
    function __construct()
    {
        parent::MY_Controller();
        $this->alAnalysis = Doctrine::GetTable('AlcheckAnalysis');
        $this->alBatch = Doctrine::GetTable('AlcheckBatch');
    }
    
    function index()
    {
        // generate html for the batch listboxes
        $nBatch = 0;
        foreach ($this->alBatch->findAllBatches() as $b) {
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
            $batch = $this->alBatch->find($id);
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
            ->leftJoin('AlcheckAnalysis a')
            ->where('b.id = ?', $batch_id);

        if (! $batch) {
            show_404('page');
        }

        $numsamples = $batch->Analysis->count();
        $data->errors = false;
        if ($refresh) {
            if ($new) {
                // add a sample to this batch and redirect
                $batch->addAnalysis();
                $_POST['new'] = false;
                $_POST['refresh'] = false;
                redirect('alcheck/sample_loading');
            }
            // valid
            $is_valid = $this->form_validation->run('al_sample_loading');

            if ($is_valid) {
                // grab postdata
                $number_within_batch = $this->input->post('number_within_batch');
                $analysis_id = $this->input->post('analysis_id');
                $sample_name = $this->input->post('sample_name');
                $bkr_number = $this->input->post('bkr_number');
                $wt_bkr_tare = $this->input->post('wt_bkr_tare');
                $wt_bkr_sample = $this->input->post('wt_bkr_sample');
                $notes = $this->input->post('notes');

                // update the batch object with our submitted data
                for ($i = 0; $i < $numsamples; $i++) {
                    $batch->AlcheckAnalysis[$i]->number_within_batch = $number_within_batch[$i];
                    $batch->AlcheckAnalysis[$i]->analysis_id = $analysis_id[$i];
                    $batch->AlcheckAnalysis[$i]->sample_name = $sample_name[$i];
                    $batch->AlcheckAnalysis[$i]->bkr_number = $bkr_number[$i];
                    $batch->AlcheckAnalysis[$i]->wt_bkr_tare = $wt_bkr_tare[$i];
                    $batch->AlcheckAnalysis[$i]->wt_bkr_sample = $wt_bkr_sample[$i];
                    $batch->AlcheckAnalysis[$i]->notes = $notes[$i];
                }

                $batch->save(); // commit it to the database
            } else {
                $data->errors = true;
            }
            
        }
        
        $data->title = 'Stage 1 of new Al check batch';
        $data->main = 'alchecks/sample_loading';
        $this->load->view('template', $data);
    }
}
<?php

class Alchecks extends MY_Controller
{
    
    var $AlAnalysis;
    
    function Alchecks()
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
    
    function sample_loading()
    {
        $batch_id = $this->input->post('batch_id');
    }
}
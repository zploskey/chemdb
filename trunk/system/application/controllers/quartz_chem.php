<?php

/**
 * Business logic for the quartz chemistry pages.
 * 
 * Methods are listed in the same order as they appear in the front page for
 * the Quartz Chemistry section (the index function).
 */

class Quartz_chem extends MY_Controller
{
	/**
	 * Contructs the class object, connects to database, and loads necessary
	 * libraries.
	 **/
	function Quartz_chem()
	{
		parent::MY_Controller();
		$this->load->model('batch');
		$this->load->model('analysis');
	}
	
	/**
	 * The main quartz chemistry page. Contains a series of select boxes and
	 * links to the other pages.
	 */
	function index()
	{	
		// build strings of html for the select boxes
		foreach ($this->batch->get_open_batches() as $b)
		{
			$data->open_batches .= "<option value=$b->id>$b->start_date $b->owner $b->description";
		}
		
		foreach ($this->batch->get_all_batches() as $b)
		{
			$data->all_batches .= "<option value=$b->id>$b->start_date $b->owner $b->description";
		}
		
		$data->title = 'Quartz Al-Be chemistry';
		$data->subtitle = 'Al-Be extraction from quartz:';
		$data->main = 'quartz_chem/index';
		$this->load->view('template', $data);
	}

	/**
	 * Form for adding a batch or editing the batch information.
	 */
	function new_batch()
	{
		$id = $this->input->post('id');
		$is_edit = (bool) $id;
		$data->allow_num_edit = ( ! $is_edit);
		
		if ($is_edit)
		{
			// it's an existing batch, get it
			$batch = $this->batch->get($id);
			
			if ( ! $batch)
			{
				show_404('page');
			}

			$data->numsamples = $batch->count();
		}
		else // it's a new batch
		{
			$batch = new stdClass();
			$data->numsamples = null;
		}

		if ($this->form_validation->run('batches') == FALSE)
		{	
			if ($is_edit)
			{
				$batch->id          = set_value('id', $batch->id);
				$batch->owner       = set_value('owner', $batch->owner);
				$batch->description = set_value('description', $batch->description);
				$batch->start_date  = set_value('start_date', $batch->start_date);
			}
			else
			{
				$batch->id          = set_value('id');
				$batch->owner       = set_value('owner');
				$batch->description = set_value('description');
				$batch->start_date  = date('Y-m-d');
			}

		}
		else // inputs are valid, save changes
		{
			// grab batch info from post and save it to the db
			$fields = array('id', 'owner', 'description', 'start_date');
			foreach ($fields as $f)
			{
				$batch->{$f} = $this->input->post($f);
			}
			
			$query = $this->batch->save($batch);
			
			if ( ! $batch->id)
			{
				// we just did an insert, grab that id
				$batch->id = $this->batch->insert_id();
				// create the analyses linked to this batch
				$numsamples = $this->input->post('numsamples');
				$this->analysis->insert_stubs($batch->id, $numsamples);
			}
		}

		// set the rest of the view data
		$data->numsamples = $this->input->post('numsamples');
		$data->title = 'Add a batch';
		$data->main = 'quartz_chem/new_batch';
		$data->batch = $batch;
		$this->load->view('template', $data);
	}
	
	function test()
	{
		echo date('Y-m-d');
	}
	
}

/* End of file quartz_chem.php */
/* Location: ./system/application/controllers/quartz_chem.php */
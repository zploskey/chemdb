<?php

/**
 * Business logic for the quartz chemistry pages.
 * 
 * Methods are listed in the same order as they appear in the front page for
 * the section (index).
 */

class Quartz_chem extends MY_Controller
{
	/**
	 * Contructs the class object, connects to database, and loads necessary
	 * libraries.
	 * 
	 **/
	function Quartz_chem()
	{
		parent::MY_Controller();
		$this->load->model('batch');
	}
	
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

	
	function new_batch()
	{
		$id = $this->input->post('id');
		$is_edit = (bool) $id;
		$data->allow_num_edit = ! $is_edit;
		
		if ($is_edit)
		{
			// it's an existing batch, get it
			echo 'we\'re here'.$id;
			$batch = $this->batch->get($id);

			$data->numsamples = $batch->count();
		}
		else // it's a new batch
		{
			$batch = new stdClass();
			$batch->start_date = date('Y-m-d');
			$data->numsamples = null;
		}
		

		
		$fields = array('id', 'owner', 'description');
		
		if ($this->form_validation->run('batches') == FALSE)
		{
			$default = null;
			// (re)set the form values
			foreach ($fields as $f)
			{
				if ($is_edit)
				{
					$default = $batch->{$f};
				}
				$batch->{$f} = set_value($f, $default);
				
				echo ' '.$batch->{$f}.' ';
			}

		}
		else // inputs are valid, save changes
		{
			$fields[] = 'start_date';
			foreach ($fields as $f)
			{
				$batch->{$f} = $this->input->post($f);
			}
			echo print_r($batch);
			$this->batch->save($batch);
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
<?php

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
			$data->open_batches .= "<option value=$b->id>$b->start_date $b->owner $b->desc";
		}
		
		foreach ($this->batch->get_all_batches() as $b)
		{
			$data->all_batches .= "<option value=$b->id>$b->start_date $b->owner $b->desc";
		}
		
		$data->title = 'Quartz Al-Be chemistry';
		$data->subtitle = 'Al-Be extraction from quartz:';
		$data->main = 'quartz_chem/index';
		$this->load->view('template', $data);
	}
	
}

/* End of file quartz_chem.php */
/* Location: ./system/application/controllers/quartz_chem.php */
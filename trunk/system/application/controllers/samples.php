<?php

class Samples extends MY_Controller
{
	/**
	 * @var Doctrine_Table corresponding table
	 */
	var $sample;

	/**
	 * Constructor for Samples
	 * 
	 * @return void
	 **/
	function Samples()
	{
		parent::MY_Controller();
		$this->sample = Doctrine::getTable('Sample');
	}

	/**
	 * Loads a page listing samples.
	 *
	 * @return void
	 **/
	function index()
	{
		// Pagination url: samples/index/sort_by/sort_dir/page
		//     URI number:     1   /  2  /   3   /    4   / 5
		//       Defaults: samples/index/  name  /  desc  / 0

		// set database pagination settings
		$sort_by = $this->uri->segment(3,'name');
		$sort_dir = strtolower($this->uri->segment(4,'ASC'));
		$page = $this->uri->segment(5,0);
		$num_per_page = 5;
		$query = Doctrine_Query::create()->from('Sample')->orderBy("$sort_by $sort_dir");
		$pager = new Doctrine_Pager($query, $page, $num_per_page);
		$samples = $pager->execute();

		// set pagination options
		$this->load->library('pagination');
		$config['base_url'] = site_url("projects/index/$sort_by/$sort_dir");
		$config['total_rows'] = $pager->getNumResults();
		$config['per_page'] = $num_per_page;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);
			
		$data = array(
			'title'        => 'Manage Samples',
			'main'         => 'samples/index',
			'samples'      => $samples,
			'pagination'   => 'Go to page: '.$this->pagination->create_links(),
			'sort_by'      => $sort_by,
			'alt_sort_dir' => switch_sort($sort_dir),  // a little trick I put in the snippet helper
			'page'         => $page
		);
	
		$this->load->view('template', $data);
	}

	/**
	 * Displays the edit form and evaluates submits. If the submit validates properly, 
	 * it makes change to sample in database and redirects.
	 *
	 */
	function edit($id = 0) 
	{	
		if ($id) {
			// edit an existing sample
			$sample = $this->sample->find($id);

			if (!$sample)
			{
				show_404('page');
			}
			
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
		
		$is_edit = (bool) $id;
		$fields = $this->sample->getFieldNames();
		unset($fields[0]); // unset the id
				
		// validate anything that was submitted
		if ($this->form_validation->run('samples') == FALSE) {
			$default = null;
			
			foreach ($fields as $f) {
				if ($is_edit) {
					$default = $sample->{$f};
				}
				
				$sample->{$f} = set_value($f, $default);
			}

			$data->sample = $sample;
			$data->main   = 'samples/edit';
			$this->load->view('template', $data);
		} else {
			// everything was valid, save changes to db and redirect
			foreach ($fields as $f) {
				$sample->{$f} = $this->input->post($f);
			}
			
			$sample->save();
			redirect('samples');
		}
	}
	
	/**
	 * Shows the data for a sample.
	 *
	 * @return void
	 */
	function view($id)
	{
		$sample = $this->sample->find($id);
		
		if ( ! $sample) {
			show_404('page');
		}
		
		$data->title = 'View Sample';
		$data->subtitle = 'Viewing '.$sample->name;
		$data->arg = $id;
		$data->sample  = $sample;
		$data->main  = 'samples/view';
		$this->load->view('template', $data);
	}
	
	// ----------
	// CALLBACKS:
	// ----------
	
	function is_unique($value, $field)
	{
		$id = $this->uri->segment(3, null);
		return $this->form_validation->is_unique($value, $field, $id);
	}

	/*
	 * Returns true if value is not greater than 180.
	 */
	function valid_latlong($value)
	{
		if (abs($value) <= 180) {
			return TRUE;
		}

		$this->form_validation->set_message('valid_latlong', 'The %s field must be from -180 to 180.');
		return FALSE;
	}

	function valid_shield_factor($value)
	{
		if ($value <= 1 && $value >= 0) {
			return TRUE;
		}

		$this->form_validation->set_message('valid_shield_factor', 'The %s field must be from 0 to 1.');
		return FALSE;
	}

}

/* End of file samples.php */
/* Location: ./system/application/controllers/samples.php */
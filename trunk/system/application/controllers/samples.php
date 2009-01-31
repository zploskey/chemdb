<?php

class Samples extends MY_Controller
{
	
	/**
	 * Constructor for Samples
	 * 
	 * @return void
	 **/
	function Samples()
	{
		parent::MY_Controller();
		$this->load->model('sample');
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
		
		// get the page from the database
		$num_per_page = 5;
		$sort_by = $this->uri->segment(3,'name');
		$sort_dir = $this->uri->segment(4,'ASC');
		$page = $this->uri->segment(5,0);
		$samples = $this->sample->get_page($page, $num_per_page, $sort_by, $sort_dir);
		
		// set pagination options
		$this->load->library('pagination');
		$config['base_url'] = site_url('samples/index/'.$sort_by.'/'.$sort_dir);
		$config['total_rows'] = $this->sample->count();
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
		if ($id) // edit an existing sample
		{
			$sample = $this->sample->get($id);

			if (!$sample)
			{
				show_404('page');
			}
			
			$data->title = 'Edit Sample';
			$data->subtitle = 'Editing '.$sample->name;
			$data->arg = $id;
		}
		else // create a new sample object
		{
			$sample = new stdClass();
			
			$data->title = 'Add Sample';
			$data->subtitle = 'Enter Sample Information:';
			$data->arg = '';
		}
		
		$is_edit = (bool) $id;
		$fields = $this->sample->list_fields();
				
		// validate anything that was submitted
		if ($this->form_validation->run('samples') == FALSE)
		{
			$default = null;
			
			foreach ($fields as $f)
			{
				if ($is_edit)
				{
					$default = $sample->{$f};
				}
				
				$sample->{$f} = set_value($f, $default);
			}

			$data->sample = $sample;
			$data->main   = 'samples/edit';
			$this->load->view('template', $data);
		}
		else // everything was valid, save changes to db and redirect
		{
			foreach ($fields as $f)
			{
				$sample->{$f} = $this->input->post($f);
			}
			
			$this->sample->save($sample);
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
		$sample = $this->sample->get($id);
		if ( ! $sample)
		{
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
		return $this->sample->is_unique($value, $field);
	}

}

/* End of file samples.php */
/* Location: ./system/application/controllers/samples.php */
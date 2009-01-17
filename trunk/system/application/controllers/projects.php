<?php

class Projects extends Controller
{
	
	/**
	 * Contructs object, connects to model, and loads necessary libraries.
	 * 
	 **/
	function Projects()
	{
		parent::Controller();
		$this->load->model('project');	
		$this->load->library('form_validation', 'session');
	}

	/**
	 * Loads page listing projects.
	 *
	 * @return void
	 **/
	function index()
	{
		$data = array(
			'projects'  => $this->project->read_all(),
			'title'    => 'Manage Projects',
			'main'     => 'projects/index'
		);

		$this->load->view('template', $data);
	}

	/**
	 * 
	 *
	 * @return void
	 **/
	function add() 
	{
		$this->_validate_project();
		
		if ($this->form_validation->run() == FALSE) 
		{	
			// (re)display form
			$data = array(
				'title' => 'Add Project',
				'main' => 'projects/add'
			);
			
			$this->load->view('template', $data);
		}
		else
		{
			$data['name'] = $this->input->post(name);
			$this->project->insert($data);
		}
	}

	/**
	 * Displays the edit form and evaluates submits. If the submit validates properly, 
	 * it makes change to project in database and redirects.
	 *
	 */
	function edit() 
	{
		$id = (int) $this->uri->segment(3);

		// display (or redisplay) the edit form
		$data = array('project' => $this->project->read($id),
		              'title'   => 'Edit Project',
		              'main'    => 'projects/edit'
		);
		
		$this->load->view('template', $data);
	}
	
	/**
	 * 
	 *
	 * @return void
	 **/
	function edit_action() 
	{	
		$data['id']   = $this->input->post('id');
		$data['name'] = $this->input->post('name');
		
		if ($this->form_validation == FALSE)
		{
			$data['main'] = 'projects/edit'.$data['id'];
			$this->load->view('template', $data);
		}
		else
		{
			// edit is valid, change the information
			$this->project->update($data);
			// $this->session->set_flashdata('message', '<div id="message">Edited successfully.</div>');
			redirect('projects');
		}
		
	}
	
	
	// VALIDATION SECTION

	/**
	 * Callback: Returns true if the name exists
	 *
	 * @param string $name
	 * @return boolean
	 */
	function name_exists($name) 
	{
		if ($this->project->count_named($name))
		{
			return TRUE;
		}
		else 
		{
			$this->validation->set_message('name_exists', 'The project %s aly exists.');
			return FALSE;
		}
	}
}


/* End of file projects.php */
/* Location: ./system/application/controllers/projects.php */
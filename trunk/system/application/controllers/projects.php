<?php

class Projects extends MY_Controller
{	
	/**
	 * @var Doctrine_Table corresponding table
	 */
	var $project;

	/**
	 * Contructs the class object, connects to database, and loads necessary libraries.
	 * 
	 **/
	function Projects()
	{
		parent::MY_Controller();
		$this->project = Doctrine::getTable('Project');
	}

	/**
	 * Loads a page listing projects.
	 *
	 * Uses pagination if there are many projects.
	 *
	 * @return void
	 **/
	function index()
	{
		// Pagination url: projects/index/sort_by/sort_dir/page
		//     URI number:     1   /  2  /   3   /    4   / 5
		//       Defaults: projects/index/  name /   asc  / 0
		
		// set database pagination settings
		$sort_by = $this->uri->segment(3, 'name');
		$sort_dir = strtolower($this->uri->segment(4, 'ASC'));
		$num_per_page = 5;
		$page = $this->uri->segment(5, 0);
		$projects = Doctrine_Query::create()
			->from('Project')
			->orderBy("$sort_by $sort_dir")
			->limit($num_per_page)
			->offset($page)
			->execute();
				
		// set pagination options
		$this->load->library('pagination');
		$config['base_url'] = site_url("projects/index/$sort_by/$sort_dir");
		$config['total_rows'] = $this->project->count();
		$config['per_page'] = $num_per_page;
		$config['uri_segment'] = 5;
		$this->pagination->initialize($config);
			
		$data = array(
			'title'        => 'Manage Projects',
			'main'         => 'projects/index',
			'projects'     => $projects,
			'pagination'   => 'Go to page: '.$this->pagination->create_links(),
			'alt_sort_dir' => switch_sort($sort_dir),  // a little trick I put in the snippet helper
			'sort_by'      => $sort_by,
			'page'         => $page // * $config['per_page']
		);

		$this->load->view('template', $data);
	}

	/**
	 * Displays the edit form and evaluates submits. If the submit validates properly, 
	 * it makes change to project in database and redirects.
	 *
	 * @param int id The id of the project to edit. Default is zero for a new project.
	 */
	function edit($id = 0) 
	{	
		// are we editing an existing project?
		if ($id) {
			// Get the project data.
			$proj = $this->project->find($id);

			// If the project doesn't exist we 404.
			if ( ! $proj) {
				show_404('page');
			}
			
			// there is a project, set the display values
			$data->title = 'Edit Project';
			$data->subtitle = 'Editing '.$proj->name;
			$data->arg = $id;
		} else {
			// it's a new project, create a new record object and other display values
			$proj = new Project();
			$data->title = 'Add Project';
			$data->subtitle = 'Enter Project Information:';
			$data->arg = '';
		}
			
		// validate anything that was submitted
		if ($this->form_validation->run('projects') == FALSE) {
			// reset form values
			$proj->name = set_value('name', $proj->name);
			$proj->description = set_value('description', $proj->description);
			
			$data->proj = $proj;
			$data->main = 'projects/edit';
			$this->load->view('template', $data);
		} else {
			// inputs are valid, save changes and redirect
			$proj->name = $this->input->post('name');
			$proj->description = $this->input->post('description');
			$proj->save();
			redirect('projects');
		}
	}
	
	/**
	 * Shows the project information.
	 *
	 * @return void
	 */
	function view($id)
	{
		$project = $this->project->find($id);
		
		if ( ! $project) {
			show_404('page');
		}
		
		$data->title = 'View Project';
		$data->subtitle = 'Viewing '.$project->name;
		$data->arg = '/'.$id;
		$data->project  = $project;
		$data->main  = 'projects/view';
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

}

/* End of file projects.php */
/* Location: ./system/application/controllers/projects.php */
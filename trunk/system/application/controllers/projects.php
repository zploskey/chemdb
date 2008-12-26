<?php

class Projects extends Controller {

	function Projects() {
		parent::Controller();
		$this->load->model('project');
	}
	
	function index() {
		$data['projects'] = $this->project->get_all();
		$this->load->view('projects_view', $data);
	}
	
	function edit($id) {
		
	}
	
	function delete($id) {
	
	}
}

/* End of file projects.php */
/* Location: ./system/application/controllers/projects.php */
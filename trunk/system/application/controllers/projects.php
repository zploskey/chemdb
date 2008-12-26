<?php

class Projects extends Controller {

	function Projects() {
		parent::Controller();
		$this->load->model('project');
	}
	
	function index() {
		$data['projects'] = $this->project->get_all();
		$this->load->view('projects/list', $data);
	}
	
	function edit($id = null) {
		if ($id != null) {
			$data['id'] = $id;
			$this->load->view('projects/edit', $data);
		}
	}
	
	function delete($id = null) {
		if ($id == null) {
			// error msg
		} else {
			// request delete from DB
		}
	}
}

/* End of file projects.php */
/* Location: ./system/application/controllers/projects.php */
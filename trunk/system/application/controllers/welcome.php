<?php

class Welcome extends MY_Controller {

	function Welcome() 
	{
		parent::MY_Controller();
	}
		
	function index()
	{
		$data = array(
			'main' => 'welcome',
			'title' => 'CNL Database'		
		);

		$this->load->view('template', $data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */

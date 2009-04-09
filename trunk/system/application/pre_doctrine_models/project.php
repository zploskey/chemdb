<?php
/**
 * Gather information on projects from the database.
 *
 * @package ChemDatabase
 **/ 
class Project extends MY_Model
{
	var $table = 'projects';
	
	/**
	 * Construct and connect to the database.
	 *
	 * @return void
	 **/
	function Project()
	{
		parent::MY_Model();
		$this->load->database('chem');
	}
		
}

/* End of file project.php */
/* Location: ./system/application/models/project.php */
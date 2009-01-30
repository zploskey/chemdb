<?php
/**
 * Access data on samples from the database.
 *
 * @package ChemDatabase
 **/ 
class Sample extends MY_Model
{	
	var $table = 'samples';
	
	/**
	 * Construct and connect to the database.
	 *
	 * @return void
	 **/
	function Sample()
	{
		parent::MY_Model();
		$this->load->database('chem');
	}

}

/* End of file sample.php */
/* Location: ./system/application/models/sample.php */
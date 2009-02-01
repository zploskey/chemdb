<?php
/**
 * Analysis Model
 *
 * @package ChemDatabase
 **/ 
class Analysis extends MY_Model 
{	
	var $table = 'analyses';
	
	function Analysis()
	{
		parent::MY_Model();
		$this->load->database('quartz_chem');
	}
	
	/**
	 * Inserts stubs into the table with links to the batch_id.
	 *
	 * @return bool True for success, false for failure of the insert.
	 **/
	function insert_stubs($batch_id, $numsamples)
	{
		$vals = '';
		for ($i = 1; $i <= $numsamples; $i++)
		{
			$vals .= "($batch_id, $i)";
			// put commas between values blocks
			if ($i != $numsamples)
			{
				$vals .= ',';
			}
		}
		
		$sql = "INSERT INTO $this->table (batch_id, number_within_batch) "
		 	 . "VALUES " . $this->db->escape_str($vals);
			
		return $this->db->query($sql);
	}
}

/* End of file project.php */
/* Location: ./system/application/models/project.php */
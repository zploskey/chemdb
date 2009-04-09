<?php
/**
 * Batch Model
 *
 * @package ChemDatabase
 **/ 
class Batch extends MY_Model 
{	
	var $table = 'batches';
	
	function Batch()
	{
		parent::MY_Model();
		$this->load->database('quartz_chem');
	}

	function get_open_batches()
	{
		return $this->db->select('id, owner, description, start_date')
			            ->from($this->table)
			            ->where('completed', 'n')
			            ->order_by('start_date', 'desc')
			            ->get()->result();
	}
	
	function get_all_batches()
	{
		return $this->db->select('id, owner, description, start_date')
			            ->from($this->table)
			            ->order_by('start_date', 'desc')
			            ->get()->result();
	}
	
}

/* End of file project.php */
/* Location: ./system/application/models/project.php */
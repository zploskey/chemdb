<?php
/**
 * Gather information on projects from the database.
 *
 * @package ChemDatabase
 **/ 
class Project extends IgnitedRecord {
	
	/**
	 * Construct the class and load the database.
	 *
	 * @return void
	 **/
	function Project() 
	{
		parent::Model();
	}
	
	
	/**
	 * Get all project records.
	 *
	 * @return object
	 **/
	function read_all() 
	{
		return $this->db->get('project');
	}
	
	/**
	 * Get an individual project.
	 *
	 * @param integer project's id number
	 * @return array
	 */
	function read($id) 
	{
		return $this->db->where('id', $id)->get('project')->row();
	}
	
	/**
	 * Set values for an individual project.
	 *
	 * @param integer project's id number
	 * @param string project name
	 * @return boolean
	 */
	function update($data) 
	{
		return $this->db->where('id', $data['id'])->update('project', $data);
	}
	
	/**
	 * Insert a project into the projects table.
	 *
	 * @param array Associative array of field => value.
	 * @return int inserted id numer or -1 if insert failed
	 **/
	function create($data) 
	{
		if($this->db->insert('project', $data)) 
		{
			return $this->db->insert_id();
		}
		else 
		{
			return -1;
		}
	}
	
	/**
	 * Returns true if project is name is already present in the database.
	 * Generally used from the callback.
	 *
	 * @param string Project name
	 * @return boolean
	 **/
	function count_named($name) 
	{
		return $this->db->where('name', $name)->count_all_results('project');
	}
	
}

/* End of file project.php */
/* Location: ./system/application/controllers/project.php */


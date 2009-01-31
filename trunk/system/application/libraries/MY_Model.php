<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Extend the CI 1.7 model with some extra features that are used more than
 * once in the application.
 *
 * @author Zach Ploskey
 * @package ChemDB
 */
class MY_Model extends Model 
{
	
	/**
	 * The associated database table--override this with the table name.
	 *
	 * @var string Table name
	 **/
	var $table = '';

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function MY_Model()
	{
		parent::Model();
	}
	
	function count()
	{
		return $this->db->count_all($this->table);
	}
	
	/**
	 * Finds the number of records returned by a query 'where field = value'.
	 *
	 * @return int number of records
	 **/
	function count_where($field, $value)
	{
		return $this->db->where($field, $value)->count_all_results();
	}
	
	/**
	 * Finds the number of records returned by a query where field = value.
	 * Supports limit and offset.
	 *
	 * @param array associative array, fieldname => value
	 * @param int maximum number of records to return
	 * @param int record to offset the page to
	 * @return int number of records
	 **/
	function get_where($where, $limit = null, $offset = null)
	{
		return $this->db->get_where($this->table, $where, $limit, $offset);
	}
	
	function get($id = null)
	{
		if ($id == null)
		{
			return $this->db->get($this->table)->result();
		}
		else
		{
			// SELECT * FROM $table WHERE ID = $id LIMIT 1
			return $this->db->get_where($this->table, array('id' => $id), 1)->row();
		}
	}
	
	function get_all()
	{
		return $this->db->get_all($this->table);
	}
	
	function save($obj)
	{
		if (isset($obj->id) AND ($obj->id > 0))
		{
			$id = $obj->id;
			unset($obj->id);
			
			$this->db->where('id', $id)->update($this->table, $obj);
		}
		else // insert a new record
		{
			return $this->db->insert($this->table, $obj);
		}
	}
	
	/**
	 * Return one page of items as an database object, sorted by the $sort_by
	 * parameter.  
	 *
	 * @param  $offset   the offset (in number of items) of the current page
	 * @param  $limit    number of items per page
	 * @param  $sort_by  the DB field to sort the results by
	 * @param  $sort_dir the sort direction, defaults to ascending ('ASC')
	 * @throws InvalidArgumentException if $sort_dir != 'ASC' or 'DESC'
	 * @return object   database field object
	 **/
	function get_page($offset, $limit, $sort_by, $sort_dir = 'ASC')
	{
		$sort_dir = strtoupper($sort_dir);
		
		if ($sort_dir != 'DESC' AND $sort_dir != 'ASC')
		{
			throw new InvalidArgumentException('Invalid parameter. The 4th '
				.'parameter should be "DESC" or "ASC".');
		}
		
		return $this->db->from($this->table)->offset($offset)->limit($limit)
			             ->order_by($sort_by, $sort_dir)->get()->result();
	}
	/**
	 * Returns an array of strings, not including first field, which is
	 * usually the ID field.
	 *
	 * @return array of strings 
	 **/
	function list_fields()
	{
		$fields = $this->list_all_fields();
		unset($fields[0]);  // take off the id field
		return $fields;
	}
	/**
	 * Same as list_fields() except it includes the id field.
	 *
	 * @return array of strings
	 **/
	function list_all_fields()
	{
		return $this->db->list_fields($this->table);
	}
	
	/**
	 * Determines if $value already exists in the table specified in $field.
	 * $field is formatted as table_name.field_name (separated with .)
	 *
	 * @param     string value to check for uniqueness in DB
	 * @param     string the database field to check
	 * @return    bool true if the value is unique
	 */    
	function is_unique($value, $field)
	{
		list($table, $column) = explode('.', $field);
		$id = $this->uri->segment(3, 0);
		
		if ($id == 0)
		{
			return FALSE; // our indices begin at 1
		}
		
		$query = $this->db->select('id')->from($this->table)->where($column, $value)->get();
		
		if ($query->num_rows() == 0)
		{
			return TRUE; // it's a unique name, sweet!
		}
		else // matched, find out if it is just the submitted item or a dupe
		{
			// Assuming that the database had just 1 match, this loop
			// should only execute once, but let's be safe just in case.
			foreach ($query->result() as $row)
			{
				if ($row->id != $id)
				{
					// id mismatch, this would have created a multiple: FAIL
					return FALSE;
				}
			}
			// this is an edit for sure, let it pass
			return TRUE;
		}
	}

}

/* End of file MY_Model.php */
/* Location: ./system/application/libraries/MY_Model.php */
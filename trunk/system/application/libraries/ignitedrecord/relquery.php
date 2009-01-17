<?php 
/*
 * Created on 2008 Jul 10
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */

/*
 * Copyright (c) 2008, Martin Wernstahl
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * The name of Martin Wernstahl may not be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY Martin Wernstahl ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Martin Wernstahl BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * An object to store the query to fetch a related object.
 * 
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @par Copyright
 * Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 */
class IR_RelQuery extends IR_base{
	/**
	 * The table the derived objects link to.
	 * Used when $no_instance is set to true.
	 */
	var $table;
	
	/**
	 * The name of the relation this uses.
	 */
	var $name;
	
	/**
	 * The instance to use when creating the objects.
	 */
	var $model_inst;
	
	/**
	 * If no model instance were found.
	 * If true, $model_inst contains the object who created this object.
	 */
	var $no_instance = false;
	
	/**
	 * If there is no relation.
	 */
	var $no_rel = false;
	
	/**
	 * If the return should be an array.
	 */
	var $multiple = false;
	
	/**
	 * Stores a cache of the number of related records.
	 */
	var $count = false;
	
	// --------------------------------------------------------------------
		
	/**
	 * Constructor
	 */
	function IR_RelQuery(){
		parent::IR_base();
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * A wrapper for the get() query method, returnig IR_records instad of db_result objects.
	 * 
	 * @return array or IR_record, depending on the type of relation
	 */
	function &get()
	{
		if($this->no_rel)
		{
			if($this->multiple)
				$ret = array();
			else
				$ret = false;
			
			return $ret;
		}
		
		// let's not let joins ruin our query
		$this->select($this->name.'.*');
		
		$query = parent::get();
		$ret =& $this->dbobj2ORM($query, ! $this->multiple);
		
		$this->count = count($ret);
		
		return $ret;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the count of all rows, or all rows mathing previously called filter methods.
	 * 
	 * @since 0.2.1
	 * @access public
	 * @return int
	 */
	function count()
	{
		if($this->count !== false)
		{
			return $this->count;
		}
		
		$this->select('COUNT(1)', false);
		$query = parent::get();
		
		$this->count = array_shift($query->row_array());
		$query->free_result();
		
		return $this->count;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * JOINs the related object(s) into the current query in construction.
	 * 
	 * They are prefixed with their relation name.
	 * The sql where will only find rows with a related row attached to them.
	 * 
	 * Note: It is only possible to join Has One or Belongs To relationships.
	 * Note: Requires IgnitedQuery
	 * 
	 * @param $name The relation name
	 * @param $columns The columns to fetch
	 * @param $type What type of join which should be used, default: left
	 * @return $this
	 */
	function join_related($name, $columns = false, $type = 'left')
	{
		if($this->no_instance OR $this->no_rel)
		{
			// just do nothing
			// Oh, wait, yell loud instead!
			IR_base::log('error', 'IgnitedRecord: There is no model instance and/or no relation tied to the table "'.
						$this->table.'", failed to join related '.$name.'.');
			return $this;
		}
		
		if(isset($this->model_inst->relations[$name]))
		{
			$this->model_inst->relations[$name]->join($this, $name, $this->model_inst, $columns, $type);
			
			if( ! $columns)
				$this->joined_tables[$name] = array('table' => $this->model_inst->relations[$name]->get_table(),
													'single' => ! $this->model_inst->relations[$name]->plural);
		}
		else
		{
			IR_base::log('error', 'IgnitedRecord: Relation "'.$name.'" was not found.');
		}
		
		return $this;
	}
}

/* End of file relquery.php */
/* Location: ./application/libraries/ignitedrecord/relquery.php */
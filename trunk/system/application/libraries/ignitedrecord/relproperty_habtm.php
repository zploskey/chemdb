<?php 
/*
 * Created on 2008 Sep 09
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
 * @addtogroup IgnitedRecord
 * @{
 */

/**
 * Stores the properties for a Has And Belongs To Many relationship.
 * 
 * @since 0.2.0
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @par Copyright
 * Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 */
class IR_RelProperty_habtm extends IR_RelProperty_has{
	/**
	 * The foreign key linking the join table with the related table.
	 */
	var $related_fk;
	
	/**
	 * The join table name.
	 */
	var $join_table;
	
	/**
	 * The table of the model that utilizes this object.
	 * Used to get the default join-table name.
	 */
	var $parent_table;
	
	/**
	 * Extra columns to fetch from the join table with the relations.
	 */
	var $attr = array();
	
	// --------------------------------------------------------------------
		
	/**
	 * Also inits the foreign columns.
	 */
	function IR_RelProperty_habtm($settings = array())
	{
		parent::IR_RelProperty($settings);
		
		foreach(array('related_foreign_key', 'related_fk', 'r_fk') as $key)
		{
			if(isset($settings[$key]))
			{
				$this->related_fk = $settings[$key];
			}
		}
		
		if(isset($settings['join_table']))
		{
			$this->join_table = $settings['join_table'];
		}
		
		foreach(array('attributes', 'attr') as $key)
		{
			if(isset($settings[$key]))
			{
				$this->attr = $settings[$key];
			}
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Sets the table used for linking the two tables together.
	 * 
	 * @param $table The table
	 * @return $this
	 */
	function &join_table($table)
	{
		$this->join_table = $table;
		return $this;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Sets extra attribute columns for the join table.
	 * 
	 * @param $col The columns to use
	 * @return $this
	 */
	function attr($col)
	{
		$this->attr = $col;
		return $this;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Sets extra attribute columns for the join table.
	 * 
	 * @param $col The columns to use
	 * @return $this
	 */
	function attributes($col)
	{
		return $this->attr($col);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the table name used in linking the two tables.
	 * 
	 * @return string
	 */
	function get_join_table()
	{
		return isset($this->join_table)
				? $this->join_table
				: (strcmp($this->parent_table, $this->get_table()) < 0
					? $this->parent_table.'_'.$this->get_table()
					: $this->get_table().'_'.$this->parent_table
				  );
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Sets the foreign key the related table relates to in the relation table.
	 * 
	 * @param $col The column
	 * @return $this
	 */
	function &related_foreign_key($col)
	{
		$this->related_fk = $col;
		return $this;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Alias for related_foreign_key().
	 * 
	 * @param $col The column
	 * @return $this
	 */
	function related_fk($col)
	{
		return $this->related_foreign_key($col);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Alias for related_foreign_key().
	 * 
	 * @param $col The column
	 * @return $this
	 */
	function r_fk($col)
	{
		return $this->related_foreign_key($col);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the column name of the column that is linking the relation table and the foreign table.
	 * 
	 * @return string
	 */
	function get_related_fk()
	{
		return isset($this->related_fk)
				? $this->related_fk
				: (($name = $this->_get_modelname($this->table))
					? $name
					: singular($this->get_table())
				  ).'_id';
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Creates a Has And Belongs To Many (habtm) relationship query.
	 * 
	 * If IgnitedQuery is used as the wrapper, this method creates a subquery
	 * to fetch all id's instead of first querying for the ids and then shoving
	 * them back to the db.
	 * 
	 * A performance improvement, to simplify the paragraph above :P
	 * 
	 * @access public
	 * @param $object The object to load relations for
	 * @param $query The object to be populated with the query
	 * @return $query
	 */
	function &build_query(&$obj, &$query)
	{
		// fetch the id column
		if($model =& IR_base::get_model($this->get_table()))
		{
			$id = $model->id_col;
		}
		else
		{
			// assume that id is the unique id
			$id = 'id';
		}
		
		// build query
		$query->from($this->_get_aliased_table());
		
		$query->join($this->get_join_table(),
				$this->get_join_table().'.'.$this->get_related_fk().' = '.$this->name.'.'.$id);
		
		// filter the result
		$query->where($this->get_join_table().'.'.$this->get_fk(), $obj->__id);
		
		// add extra attribute columns
		// TODO: move this to the IR_RelQuery object, to avoid problems with count()
		foreach( (Array) $this->attr as $col)
		{
			$query->select($this->get_join_table().'.'.$col);
		}
		
		return $query;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * JOINs a related has * relationship into the queryobejct.
	 * 
	 * @access private
	 * @param $q_obj The query object to add the code to
	 * @param $name The name of the relation
	 * @param $rel The relation property obejct
	 * @param $columns The columns to fetch, default: all
	 * @param $type What type of join which should be used, default: left
	 */
	function join(&$q_obj, $name, $parent, $columns = false, $type = 'left')
	{
		$table = $this->get_table();
		$join_table = $this->get_join_table();
		
		// fetch the columns if they are not already specified
		empty($columns) AND $columns = IR_base::list_fields($table);
		
		// get the id column for the related object
		if($model =& IR_base::get_model($table))
		{
			$col = $model->id_col;
		}
		else
		{
			$col = 'id';
		}
		
		// alias the columns
		$select = array();
		foreach((Array)$columns as $field)
		{
			$select[] = $this->name.'.'.$field.' AS '.$name.'_'.$field;
		}
		
		// add extra attribute columns
		foreach( (Array) $this->attr as $col)
		{
			$query->select($join_table.'.'.$col);
		}
		
		// add the data to the query
		$q_obj->select($select);
		$q_obj->join($join_table,
				$parent->table.'.'.$parent->id_col.' = '.$join_table.'.'.$this->get_fk(), 'left');
		$q_obj->join($this->_get_aliased_table(),
				$join_table.'.'.$this->get_related_fk().' = '.$this->name.'.'.$col, $type);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Establishes a Has One/Many relationship between $child and $object.
	 * 
	 * @param $child The record using this relation object
	 * @param $object The related object
	 * @param $extra Extra attributes for the relation establishment
	 * @return boolean
	 */
	function establish(&$child, &$object, $extra = array())
	{
		if($this->get_table() != $object->__table){
			// no matching table
			IR_base::log('error', 'IgnitedRecord: The table '.$child->__table.
								' has not got a relation with the table '.$object->__table);
			return false;
		}
		
		if( ! isset($child->__id) && ! $child->save()){
			return false;
		}
		
		if( ! isset($object->__id) && ! $object->save()){
			return false;
		}
		
		$q = new IgnitedQuery();
		
		$q->where($this->get_fk(), $child->__id);
		$q->where($this->get_related_fk(), $object->__id);
		$query = $q->get($this->get_join_table());
		
		$success = true;
		
		if( ! $query->num_rows()){
			// no relationship established
			
			foreach( (Array) $this->attr as $col)
			{
				if(isset($extra[$col]))
					$data[$col] = $extra[$col];
			}
			
			$data[$this->get_fk()] = $child->__id;
			$data[$this->get_related_fk()] = $object->__id;
			
			$success = $q->insert($this->get_join_table(), $data);
		}
		
		$query->free_result();
		
		return $success;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Destroys the relationship between $child and $object.
	 * 
	 * @param $child The record using this relation object
	 * @param $object The related object
	 */
	function destroy(&$child, &$object)
	{
		if($this->get_table() != $object->__table){
			// no matching table
			IR_base::log('error', 'IgnitedRecord: The table '.$child->__table.
								' has not got a has and belongs to relation with the table '.$object->__table);
			return;
		}
		
		if( ! isset($child->__id) || ! isset($object->__id))
			return;
		
		$q = new IgnitedQuery();
		
		$q->where($this->get_fk(), $child->__id);
		$q->where($this->get_related_fk(), $object->__id);
		$q->delete($this->get_join_table());
	}
}

/**
 * @}
 */

/* End of file relproperty_habtm.php */
/* Location: ./application/libraries/ignitedrecord/relproperty_habtm.php */
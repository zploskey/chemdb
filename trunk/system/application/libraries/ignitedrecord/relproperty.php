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
 * @addtogroup IgnitedRecord
 * @{
 */
/**
 * Stores the properties for a Belongs To relationship.
 * 
 * @since 0.2.0
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @par Copyright
 * Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 */
class IR_RelProperty{
	/**
	 * The relation name of this relation.
	 */
	var $name;
	
	/**
	 * The table to relate to.
	 * @access private
	 */
	var $table;
	
	/**
	 * The column that the other column relates to.
	 * @access private
	 */
	var $fk;
	
	/**
	 * Defines if the deletes should cascade to the related records.
	 * @access private
	 */
	var $cascade_on_delete = false;
	
	// --------------------------------------------------------------------
		
	/**
	 * Loads the settings array.
	 *
	 * @param $settings The settings
	 */
	function IR_RelProperty($settings = array())
	{
		if(isset($settings['table']))
		{
			$this->table = $settings['table'];
		}
		
		if(isset($settings['foreign_key']))
		{
			$this->fk = $settings['foreign_key'];
		}
		
		if(isset($settings['fk']))
		{
			$this->fk = $settings['fk'];
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Initializes this model with a singular name.
	 * 
	 * The singular is assigned to $this->model if it isn't already set
	 * 
	 * @access private
	 * @param $plural The name
	 */
	function singular($singular)
	{
		if( ! isset($this->table))
			$this->table = plural($singular);
		
		$this->name = $singular;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Sets the table name for this relation.
	 * 
	 * @param $table The table name
	 * @return $this
	 */
	function &table($table)
	{
		$this->table = $table;
		return $this;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Sets the foreign key column for this relation.
	 * 
	 * @param $col The column name
	 * @return $this
	 */
	function &foreign_key($col)
	{
		$this->fk = $col;
		return $this;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Sets the foreign key column for this relation.
	 * 
	 * Alias for foreign_key().
	 * 
	 * @param $col The column name
	 * @return $this
	 */
	function &fk($col)
	{
		return $this->foreign_key($col);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Defines if the deletes should cascade to the related records.
	 * 
	 * @param $value true/false
	 * @return $this
	 */
	function cascade_on_delete($value)
	{
		$this->cascade_on_delete = $value;
		return $this;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the table used by this relation.
	 * 
	 * @return string
	 */
	function get_table()
	{
		return $this->table;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the foreign key used by this relation.
	 * 
	 * @return string
	 */
	function get_fk()
	{
		return isset($this->fk)
				? $this->fk
				: (($name = $this->_get_modelname($this->table))
					? $name
					: singular($this->get_table())
				  ).'_id';
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * 
	 */
	function get_cascade_on_delete()
	{
		return $this->cascade_on_delete;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Creates a Belongs To relationship query.
	 * 
	 * @access public
	 * @param $object The object to load relations for
	 * @param $query The object to be populated with the query
	 * @return $query
	 */
	function &build_query(&$obj, &$query)
	{
		if(isset($obj->{$this->get_fk()}))
		{
			$query->from($this->_get_aliased_table());
			$query->limit(1);
			
			if($model =& IR_base::get_model($this->get_table()))
			{
				$query->where($this->name.'.'.$model->id_col, $obj->{$this->get_fk()});
			}
			else
			{
				// assume that id is the unique id
				$query->where($this->name.'.id', $obj->{$this->get_fk()});
			}
		}
		else
		{
			IR_base::log('debug', 'The column "'.$this->get_fk().
				'" was not set in the table '.$obj->__table.' on uid '.$obj->__id);
		}
		
		return $query;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Creates a Belongs To relationship query object.
	 * 
	 * @access public
	 * @param $object The object to load relations for
	 * @param $query The object to be populated with the query
	 * @return $query
	 */
	function &build_relquery(&$obj, &$rel_query)
	{
		$this->build_query($obj, $rel_query);
		
		if(isset($obj->{$this->get_fk()}))
		{
			if($model =& IR_base::get_model($this->get_table()))
			{
				// use that model to instantiate the objects
				$rel_query->model_inst =& $model;
			}
			else
			{
				// no model = normal orm OBJ
				$rel_query->no_instance = true;
				$rel_query->model_inst =& $this;
			}
			
			$rel_query->name = $this->name;
			$rel_query->table = $this->get_table();
			$rel_query->multiple = false;
		}
		
		return $rel_query;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * JOINs a related has one relationship into the queryobejct.
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
		// fetch the columns if they are not already specified
		empty($columns) AND $columns = IR_base::list_fields($this->get_table());
		
		// get the id column for the related object
		if($model =& IR_base::get_model($this->get_table()))
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
		
		// add the data to the query
		$q_obj->select($select);
		$q_obj->join($this->_get_aliased_table(), $parent->table.'.'.$this->get_fk().
					 ' = '.$this->name.'.'.$col, $type);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Establishes a Belongs To relationship between $child and $object.
	 * 
	 * @param $child The record using this relation object
	 * @param $object The related object
	 * @return boolean
	 */
	function establish(&$child, &$object)
	{
		// $child Belongs To $object
		if($this->get_table() != $object->__table){
			// no matching table
			IR_base::log('error', 'IgnitedRecord: The table '.$child->__table.' has not got a relation with the table '.$object->__table);
			return false;
		}
		
		$column = $this->get_fk();
		
		if( ! isset($object->__id)){
			// object does not exist in database, save it
			if( ! $object->save())
				return false;
		}
		
		// object now exists in database
		$child->$column = $object->__id;
		
		return $child->save();
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
		// $child Belongs To $object
		if($this->get_table() != $object->__table){
			// no matching table
			IR_base::log('error', 'IgnitedRecord: The table '.$child->__table.
								' has not got a relation with the table '.$object->__table);
			return;
		}
		
		$column = $this->get_fk();
		
		if( ! isset($object->__id) || ! isset($child->$column))
			return;
		
		if($child->$column == $object->__id){
			$child->$column = null;
			return $child->save();
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the modelname of model linked to $tablename.
	 * 
	 * 
	 * @access private
	 * @param $tablename The tablename
	 * @return The modelname or singlualr($tablename) if no model is found
	 */
	function _get_modelname($tablename)
	{
		$tablename = strtolower($tablename);
		
		if($model =& IR_base::get_model($tablename))
		{
			$name = $model->_get_modelname();
			
			return empty($name) ? singular($tablename) : $name;
		}
		else
		{
			return singular($tablename);
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the tablename of this table with an alias, if the relation name and table is not identical.
	 * 
	 * @return string
	 */
	function _get_aliased_table()
	{
		$table = $this->get_table();
		
		if($table == $this->name)
		{
			return $table;
		}
		else
		{
			return $this->get_table().' AS '.$this->name;
		}
	}
}

/**
 * @}
 */

/* End of file relproperty.php */
/* Location: ./application/libraries/ignitedrecord/relproperty.php */
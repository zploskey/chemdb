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
 * Stores the properties for a Has ... relationship.
 * 
 * @since 0.2.0
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @par Copyright
 * Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 */
class IR_RelProperty_has extends IR_RelProperty{
	/**
	 * The model that utilizes this object.
	 * Used to get the default column name to relate with.
	 */
	var $parent_model;
	
	/**
	 * Specifies if it's a has one or a has many relation.
	 */
	var $plural = false;
	
	// --------------------------------------------------------------------
		
	/**
	 * Initializes this model with a plural name.
	 * 
	 * The plural is assigned to $this->table if it isn't already set
	 * 
	 * @access private
	 * @param $plural The name
	 */
	function plural($plural)
	{
		$this->plural = true;
		
		if( ! isset($this->table))
			$this->table = $plural;
		
		$this->name = $plural;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the column used by this relation.
	 * 
	 * @return string
	 */
	function get_fk()
	{
		return isset($this->fk)
					? $this->fk
					: (($name = $this->parent_model->_get_modelname())
						? $name
						: singular($this->table)
					  ) . '_id';
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Creates a Has One/Many relationship query.
	 * 
	 * @access public
	 * @param $object The object to load relations for
	 * @param $query The object to be populated with the query
	 * @return $query
	 */
	function &build_query(&$obj, &$query)
	{
		$query->from($this->_get_aliased_table());
		
		if( ! $this->plural)
			$query->limit(1); // this is a has ONE relationship
		
		$query->where($this->name.'.'.$this->get_fk(), $obj->__id);
		
		return $query;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Creates a Has One/Many relationship query object.
	 * 
	 * @access public
	 * @param $object The object to load relations for
	 * @param $rel_query The object to be populated with the query
	 * @return $rel_query
	 */
	function &build_relquery(&$obj, &$rel_query)
	{
		$rel_query->name = $this->name;
		$rel_query->table = $this->get_table();
		$rel_query->multiple = $this->plural;
		
		if($model =& IR_base::get_model($this->get_table()))
		{
			// use that model to instantiate the objects
			$rel_query->model_inst =& $model;
		}
		else
		{
			// no model = normal orm OBJ
			$rel_query->no_instance = true;
		}
		
		$this->build_query($obj, $rel_query);
		
		return $rel_query;
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
		// fetch the columns if they are not already specified
		empty($columns) AND $columns = IR_base::list_fields($this->get_table());
		
		// alias the columns
		$select = array();
		foreach((Array)$columns as $field)
		{
			$select[] = $this->name.'.'.$field.' AS '.$name.'_'.$field;
		}
		
		// add the data to the query
		$q_obj->select($select);
		$q_obj->join($this->_get_aliased_table(), $parent->table.'.'.$parent->id_col.
					 ' = '.$this->name.'.'.$this->get_fk(), $type);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Establishes a Has One/Many relationship between $child and $object.
	 * 
	 * @param $child The record using this relation object
	 * @param $object The related object
	 * @return boolean
	 */
	function establish(&$child, &$object)
	{
		// $child Has Many of $object
		// Like an inverted Belongs To relationship
		if($this->get_table() != $object->__table){
			// no matching table
			IR_base::log('error', 'IgnitedRecord: The table '.$child->__table.' has not got a relation with the table '.$object->__table);
			return false;
		}
		
		$column = $this->get_fk();
		
		if( ! isset($child->__id)){
			// this object does not exist in database, save it
			if( ! $child->save())
				return false;
		}
		else
		{
			if( ! $this->plural)
			{
				$q = new IgnitedQuery();
				
				$q->where($this->get_table().'.'.$this->get_fk(), $obj->__id);
				$q->set($column, null);
				$q->update($this->get_table());
			}
		}
		
		// this object now exists in database
		$object->$column = $child->__id;
		
		return $object->save();
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
		// $child Has One/Many of $object type
		if($this->get_table() != $object->__table){
			// no matching table
			IR_base::log('error', 'IgnitedRecord: The table '.$child->__table.
								' has not got a relation with the table '.$object->__table);
			return;
		}
		
		$column = $this->get_fk();
		
		// works exactly like Has Many (only when establishing a relationship is it different)
		if( ! isset($child->__id) || !isset($object->$column))
			return;
		
		if($object->$column == $child->__id){
			$object->$column = null;
			return $object->save();
		}
	}
}

/**
 * @}
 */

/* End of file relproperty_habtm.php */
/* Location: ./application/libraries/ignitedrecord/relproperty_habtm.php */
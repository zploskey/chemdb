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
 * An ORM object representing a database record, extend to add extra functionality.
 * 
 * Interacts closely with an IgnitedRecord object
 * 
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @par Copyright
 * Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 */
class IR_record{
	/**
	 * The IgnitedRecord instance that manages this record.
	 * 
	 * @access private
	 */
	var $__instance;
	
	/**
	 * The table used storing this data.
	 * 
	 * @access private
	 */
	var $__table;
	
	/**
	 * The database id for this object's row.
	 * 
	 * @access private
	 * 
	 * Use uid(), in_db() or the primary key property
	 * 
	 * null if no corresponding row exists
	 */
	var $__id = null;
	
	/**
	 * The data this object was instantiated with.
	 * 
	 * @access private
	 * 
	 * Used to check if the object was changed.
	 */
	var $__data = array();
	
	/**
	 * Contains the data not to be escaped.
	 * 
	 * @access private
	 */
	var $__no_escape_data = array();
	
	// --------------------------------------------------------------------
		
	/**
	 * Constructor.
	 * 
	 * @param $instance The IgnitedRecord instance, or the tablename (for read only objects)
	 * @param $data The data this object shall be created from (only data from database)
	 * @param $id The id for the matching database row
	 */
	function IR_record(&$instance, $data = false, $id = false)
	{
		if(is_string($instance))
		{
			$this->__table = $instance;
		}
		else
		{
			$this->__instance =& $instance;
			$this->__table = $instance->table;
		}
		
		if( ! empty($id)) // false is considered to be empty
		{
			$this->__id = $id;
		}
		
		if($data != false)
		{
			$this->load_data($data);
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Omits all "resource" properties.
	 * 
	 * @since 0.2.0
	 */
	function __sleep()
	{
		return array_diff(array_keys(get_object_vars($this)),array('__instance'));
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Restores $__instance.
	 * 
	 * @since 0.2.0
	 */
	function __wakeup()
	{
		if($model = IR_base::get_model($this->__table))
		{
			$this->__instance =& $model;
		}
		else
		{
			IR_base::log('error','The model linked to the table "'.
				$this->__table.'" was not found, resulting in a read only object.');
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Dumps the contents of this object.
	 * 
	 * Marks the properties which are changed.
	 * 
	 * @param $var The value to be dumped
	 * @param $i_char The character(s) used to indent one step
	 * @return void
	 */
	function dump($i_char = '    ', $br = "\n")
	{
		idump($this, $i_char, $br);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns true if this row is in the database, false otherwise.
	 * 
	 * @since 0.1.0 RC 2
	 */
	function in_db()
	{
		return isset($this->__id);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the value of the unique identifier of the current row.
	 * 
	 * To prevent from accidentally editing the uid of the row.
	 *
	 * @deprecated (use the property instead)
	 * @since 0.1.1
	 * @return the value of the id column(s) (false if not in db)
	 */
	function uid()
	{
		return $this->in_db() ? $this->__id : false;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Loads the data in $data into this object.
	 * 
	 * @since 0.2.0
	 * @access public
	 * @param $data The data to be loaded, with column name as key, data as value
	 * @return void
	 */
	function load_data($data = array())
	{
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Loads the data from POST into this object.
	 * 
	 * @param $columns an array with columns to fetch data from: key (optional) is form field name, value is database column name
	 * @return $this
	 */
	function &load_postdata($columns)
	{
		$CI =& IR_base::controller_instance();
		
		foreach((Array)$columns as $field => $col)
		{
			if(is_numeric($field))
				$field = $col;
			
			if($CI->input->post($field) !== false)
				$this->$col = $CI->input->post($field);
		}
		
		return $this;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns an array containing all the row data from this object.
	 * 
	 * @since 0.2.0
	 * @access public
	 * @return array
	 */
	function get_data()
	{
		return $this->__instance->_strip_data($this);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns true if the supplied property has been changed.
	 *
	 * @param $prop The propertyname to check
	 * @return bool
	 */
	function is_changed($prop)
	{
		return (isset($this->$prop) XOR isset($this->__data[$prop])) OR
			   (isset($this->$prop) && isset($this->__data[$prop]) && $this->$prop != $this->__data[$prop]);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Saves a value as NON escape, which says that it won't be escaped when sent to database.
	 * 
	 * @access public
	 * @param $col The column name
	 * @param $value The value
	 * @return void
	 */
	function no_escape($col, $value)
	{
		$this->__no_escape_data[$col] = $value;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Saves this object in the database using the IgnitedRecord instance.
	 * 
	 * If this object does not exists in the database, an insert is performed,
	 * otherwise an update is performed.
	 * 
	 * @access public
	 * @param $force If to force a save, if true save() saves even if it hasen't been edited
	 * 
	 * @return true upon success, false otherwise
	 */
	function save($force = false)
	{
		if(isset($this->__instance))
		{
			return $this->__instance->save($this,$force);
		}
		else
		{
			IR_base::log('error','IR_record: No model is associated with the table '.$this->__table);
			return false;
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Updates the data in this object.
	 * 
	 * @access public
	 * Not to be confused with the SQL update, this function
	 * refreshes the data in this object.
	 * 
	 * @note All loaded relations will need to be reloaded after an update
	 * (if you want to use them).
	 */
	function update()
	{
		if(isset($this->__instance))
		{
			$this->__instance->update($this);
		}
		else
		{
			IR_base::log('error','IR_record: No model is associated with the table '.
								$this->__table);
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Deletes this object from the database.
	 * 
	 * @access public
	 * Removes all relations and bindings to the database.
	 * Can then be inserted into the table again, using save().
	 * 
	 * @note No data in this object (apart from relations and database id) are lost.
	 */
	function delete()
	{
		if(isset($this->__instance) && $this->in_db())
		{
			$this->__instance->delete($this);
		}
		else
		{
			IR_base::log('error','IR_record: No model is associated with the table '.
								$this->__table);
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns a query which fetches the related objects for the relation with the name $name.
	 * 
	 * @since 0.2.0
	 * @access public
	 * @param $name The relation name to generate a relation query for
	 * @return An IR_RelQuery object
	 */
	function &related($name)
	{
		if(isset($this->__instance))
		{
			return $this->__instance->load_rel($this, $name);
		}
		else
		{
			IR_base::log('error','IR_record: No model is associated with the table '.
								$this->__table);
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Alias for related()
	 * 
	 * @since 0.2.0
	 * @access public
	 * @param $name The relation name to generate a relation query for
	 * @return An IR_RelQuery object
	 */
	function &rel($name)
	{
		return $this->related($name);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Establishes a relationship with the supplied IR_record object.
	 * 
	 * Depending on the settings of the IgnitedRecord instance tied to this object,
	 * the type of relation can vary. \n
	 * Ex. \n
	 * If you have defined two relations, one to "posts" with Has Many
	 * and one to "moderators" with Has And Belongs To Many,
	 * and you pass an object of the type "posts", a Has Many relation
	 * will be established. \n
	 * If there are no relation defined, no relationship will be formed.
	 * 
	 * @access public
	 * @param $object An IR_record
	 * @param $rel_name The name of the relation to add the object through, if omitted,
	 *                  IgnitedRecord will try to figure it out
	 */
	function add_relationship(&$object, $rel_name = false, $attr = array())
	{
		if(isset($this->__instance))
		{
			return $this->__instance->establish_relationship($this, $object, $rel_name, $attr);
		}
		else
		{
			IR_base::log('error','IR_record: No model is associated with the table '.
								$this->__table);
			return false;
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Somewhat of a shorthand for add_relationship(), but needs the relationship name.
	 * 
	 * Example:
	 * @code
	 * $obj->add('child', $object);
	 * @endcode
	 * 
	 * @access public
	 * @param $rel_name The name of the relationship
	 * @param $object An IR_record to establish a relation with
	 * @param $attributes Extra attributes for the relation establishment
	 */
	function add($rel_name, &$object, $attributes = array())
	{
		return $this->add_relationship($object, $rel_name, $attributes);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Removes the relationship between $this and $object.
	 * 
	 * If a relationship exists between the two, it is removed.
	 * 
	 * @access public
	 * @param $object An IR_record
	 * @param $rel_name The name of the relation to remove the object from
	 */
	function remove_relationship(&$object, $rel_name = false)
	{
		if(isset($this->__instance))
		{
			$this->__instance->remove_relationship($this, $object, $rel_name);
		}
		else
		{
			IR_base::log('error','IR_record: No model is associated with the table '.
								$this->__table);
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Somewhat of a shorthand for remove_relationship(), but needs the relationship name.
	 * 
	 * Example:
	 * @code
	 * $obj->remove('child', $object);
	 * @endcode
	 * 
	 * @access public
	 * @param $rel_name The name of the relationship
	 * @param $object An IR_record to break a relation with
	 */
	function remove($rel_name, &$object)
	{
		$this->remove_relationship($object, $rel_name);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Aggregates the child helpers into this object, only PHP 5.
	 * 
	 * This method does only work in PHP 5+. Ignore it if you're using PHP 4
	 * 
	 * @param $method The method called
	 * @param $args The argument array sent to the method
	 * @return Whatever the helper method returns
	 */
	function __call($method,$args)
	{
		foreach(array_keys($this->__instance->child_class_helpers) as $property)
		{
			if(method_exists($this->$property, $method))
				return call_user_func_array(array($this->$property, $method),$args);
		}
		
		$trace = debug_backtrace();
		
		IR_base::show_error("IR_record: Method $method() is not found.<br />\nCalled on line {$trace[1]['line']} in {$trace[1]['file']}.");
	}
}
/**
 * @}
 */

/* End of file record.php */
/* Location: ./application/libraries/ignitedrecord/record.php */
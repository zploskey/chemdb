<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Created on 2008 Jul 9
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
 * Load IgnitedQuery.
 */
if( ! class_exists('IgnitedQuery'))
{
	require_once APPPATH.'libraries/ignitedquery.php';
}

/**
 * Define the global var to store the models.
 */
$GLOBALS['IR_MODELS'] = array();

/**
 * PHP 4 base for IgnitedRecord.
 */
class IR_base extends IgnitedQuery{
	
	/**
	 * Assign DB.
	 */
	function IR_base(){
		$CI =& get_instance();
		
		parent::IgnitedQuery();
	}
	
	/**
	 * Returns the model which is mapped to the table $table.
	 * 
	 * Can be called statically.
	 * 
	 * @param $table A table name
	 * @return object or false
	 */
	function &get_model($table)
	{
		if( ! isset($GLOBALS['IR_MODELS'][$table]))
		{
			$false = false;
			return $false;
		}
		
		return $GLOBALS['IR_MODELS'][$table];
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Adds a model which is mapped to the $table to the internal array.
	 * 
	 * Can be called statically.
	 * 
	 * Can add any type of model, but it needs to be able to handle calls
	 * from related IgnitedRecord instances.
	 * 
	 * @param $object The model object
	 * @param $table The tablename
	 */
	function add_model(&$object, $table)
	{
		$GLOBALS['IR_MODELS'][$table] =& $object;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the column names of the table.
	 * 
	 * @return array
	 */
	function list_fields($table)
	{
		$model =& IR_base::get_model($table);
		$CI =& IR_base::controller_instance();
		
		if($model)
		{
			if( ! isset($model->columns))
				$model->columns = $CI->db->list_fields($table);
			
			return $model->columns;
		}
		else
		{
			return $CI->db->list_fields($table);
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Stores the joined tables for each query and their prefixes.
	 * 
	 * @access private
	 */
	var $joined_tables = array();
	
	/**
	 * If dbobj2ORM() should use duplicate db rows.
	 */
	var $use_duplicate_rows = false;
	
	// --------------------------------------------------------------------
		
	/**
	 * Tells the dbobj2ORM() method to create a separate object for each row in the result array.
	 * 
	 * This value is reset after the dbobj2ORM() method is called.
	 * 
	 * @param $val If to use it or not (true/false)
	 * @return $this
	 */
	function use_duplicates($val = true)
	{
		$this->use_duplicate_rows = $val;
		
		return $this;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Creates data objects from a database result object.
	 * 
	 * Splits up data from joined tables defined in $this->joined_tables.
	 * Syntax:
	 * array('relation name' => array('table' => 'tablename',
	 *                                'single' => if it is a has one/belongs to relation))
	 * or array('relation name' => 'tablename')
	 * or array('tablename')
	 * 
	 * The relation name (plus an underscore) is expected to prefix the related records,
	 * if not do not define a relation name in joined_tables.
	 * 
	 * @param $db_obj The database object
	 * @param $ret_single If to return a single record instead of an array
	 * @return array (empty if fail) or obejct (false if fail)
	 */
	function &dbobj2ORM(&$db_obj, $ret_single = false)
	{
		// normaize settings
		$joined_tables = array();
		
		// normailze everything here to avoid n^2 complexity
		foreach((Array) $this->joined_tables as $name => $tbl)
		{
			$data = array();
			
			// normalize table
			if(is_array($tbl) && isset($tbl['table']))
			{
				$data['table'] = $tbl['table'];
			}
			else
			{
				$data['table'] = $tbl;
			}
			
			// set a prefix if defined
			$data['prefix'] = (empty($name) OR is_numeric($name)) ? '' : $name.'_';
			
			// get the model instance, child class name and id columns
			if($joined_model =& IR_base::get_model($data['table']))
			{
				$data['id_cols'] = (Array) $joined_model->id_col;
				$data['class'] = $joined_model->child_class;
				$data['model'] =& $joined_model;
			}
			else
			{
				// defaults
				$data['id_cols'] = array('id');
				$data['class'] = 'IR_record';
				
				$data['model'] = $data['table'];
			}
			
			// check if joined data should be detected with prefixes
			if($joined_model != false && $joined_model->join_detect_w_prefix == true)
			{
				$data['fields'] = true;
			}
			else
			{
				$data['fields'] = IR_base::list_fields($data['table']);
			}
			
			// property name and 
			$data['name'] = ! is_numeric($name) ? $name : $data['table'];
			$data['single'] = (isset($tbl['single']) && $tbl['single'] == true) ? true : false;
			
			$joined_tables[] =& $data;
			
			// remove reference
			unset($data);
		}
		
		$result = array();
		
		// get the model instance and the id column name
		if($model =& IR_base::get_model($this->table))
		{
			$id_col = (Array) $model->id_col;
			$mclass = $model->child_class;
		}
		else
		{
			// defaults
			$id_col = array('id');
			$mclass = 'IR_record';
			
			// create fake model
			$model = $this->table;
		}
		
		// fetch results
		foreach($db_obj->result_array() as $row_count => $row)
		{
			$id = array();
			
			// extract PK
			foreach($id_col as $col)
			{
				$id[] = isset($row[$col]) ? $row[$col] : false;
			}
			
			// Currently a dirty fix for relations:
			if(count($id) == 1)
				$id = $id[0];
			
			// create an identification, use the per row identification if set to
			$sid = $this->use_duplicate_rows ? $row_count : serialize($id);
			
			// do we have an instance already?
			if( ! isset($result[$sid]))
			{
				// nope, create one
				$result[$sid] =& new $mclass($model, array(), $id);
			}
			
			// create joined objects
			foreach($joined_tables as $data)
			{
				// reset
				$rec_data = array();
				$id = array();
				$prefix =& $data['prefix'];
				$name =& $data['name'];
				$class =& $data['class'];
				
				// extract PK
				$id = array();
				foreach($data['id_cols'] as $col)
				{
					if(isset($row[$prefix.$col]))
						$id[] = $row[$prefix.$col];
				}
				
				// Currently a dirty fix for relations:
				if(count($id) == 1)
					$id = $id[0];
				
				// extract data
				if(is_array($data['fields']))
				{
					// use the defined columns
					foreach($data['fields'] as $field)
					{
						if(array_key_exists($prefix.$field, $row))
						{
							$rec_data[$field] = $row[$prefix.$field];
							unset($row[$prefix.$field]);
						}
					}
				}
				else
				{
					// go with prefix
					foreach($row as $key => $val)
					{
						if(strpos($key, $prefix) === 0)
						{
							$rec_data[$key] = $val;
							unset($row[$key]);
						}
					}
				}
				
				// if we don't have an id, do not create a related object
				if(empty($id))
					continue;
				
				// instantiate object
				$o =& new $class($data['model'], $rec_data, $id);
				
				// save a copy of the data to determine if we have altered the object on save()
				$o->__data = $rec_data;
				
				if(is_object($data['model']))
				{
					// add the child class helpers to the record
					foreach($joined_model->child_class_helpers as $name => $hclass)
					{
						$o->$name = new $hclass($o); // PHP 4: $obj->$name =& new $hclass($o);
					}
				}
				
				// set what type of relation it is (single or multiple)
				if($data['single'] == true)
				{
					$result[$sid]->$name =& $o;
				}
				else
				{
					$tmp =& $result[$sid]->$name;
					$tmp[] =& $o;
					unset($tmp);
				}
				
				unset($rec_data);
				unset($o);
				
				// remove references
				unset($prefix);
				unset($name);
				unset($class);
			}
			// end foreach($joined_tables)
			
			if(empty($result[$sid]->__data))
			{
				$result[$sid]->load_data($row);
				
				// save a copy of the data to determine if we have altered the object on save()
				$result[$sid]->__data = $row;
				
				if(is_object($model))
				{
					// add the child class helpers to the record
					foreach($model->child_class_helpers as $name => $hclass)
					{
						$result[$sid]->$name = new $hclass($result[$sid]); // PHP 4: $result[$sid]->$name =& new $hclass($result[$sid]);
					}
				}
			}
		}
		// end foreach($db_obj->result_array())
		
		// correct return types
		if($ret_single)
		{
			if( ! empty($result))
			{
				$result =& array_shift($result);
			}
			else
			{
				$result = false;
			}
		}
		
		// reset
		$this->joined_tables = array();
		$this->use_duplicate_rows = false;
		
		// clear db object
		$db_obj->free_result();
		
		return $result;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the controller instance.
	 * 
	 * Can be called statically.
	 * 
	 * @return object
	 */
	function &controller_instance()
	{
		return get_instance();
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Calls the logging function with the message sent.
	 * 
	 * Called statically
	 * 
	 * @param $severity The severity of the message (info, debug or error)
	 * @param $message The message string
	 * @return void
	 */
	function log($severity, $message)
	{
		log_message($severity, $message);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Displays the message to the user in an error page.
	 * 
	 * @param $meessage The message to display
	 */
	function show_error($message)
	{
		show_error($message);
	}
}
/**
 * @}
 */

/* End of file base.php */
/* Location: ./application/libraries/ignitedrecord/base.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Created on 2008 Mar 28
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */

/**
 * @page BSD_LICENSE BSD License
 * @code
 * 
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
 * 
 * @endcode
 */

/**
 * @addtogroup IgnitedRecord
 * @{
 * An Object-Relational-Mapper library, utilizing the DataMapper pattern.
 * Made for the PHP framework CodeIgniter.
 * 
 * @version 1.0.0
 * 
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * 
 * @par Copyright
 * Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 * 
 * @par License:
 * Released under the BSD Lisence: @ref BSD_LICENSE
 * 
 * @link http://www.assembla.com/spaces/IgnitedRecord
 * 
 * @par PHP Version required:
 * PHP 4.3.2 or greater
 */

/**
 * Include the wrapper for IgnitedQuery, and the version specific code.
 */
if(floor(phpversion()) < 5)
{
	require_once 'base.php';
}
else
{
	require_once 'base_php5.php';
}

/**
 * Include the objects to hold data.
 */
require_once 'record.php';

/**
 * Include relation property classes.
 */
require_once 'relproperty.php';
require_once 'relproperty_has.php';
require_once 'relproperty_habtm.php';

/**
 * Include relation fetching object.
 */
require_once 'relquery.php';

/**
 * A model for ORM, extend to use.
 * 
 * Uses the CodeIgniter inflector helper to pluralize / singularize modelname / tablename.
 * 
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @par Copyright
 * Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 * 
 * @todo Edit the relation code to support multiple primary keys
 */
class IgnitedRecord extends IR_base{
	/**
	 * The table that stores all database rows.
	 * 
	 * Default is plural of classname (using CodeIgniters Inflector helper) \n
	 * Override to specifically define the tablename
	 */
	var $table = null;
	
	/**
	 * The id column in database.
	 * 
	 * Usually expected to be an auto_incremental unsigned int,
	 * IgnitedRecord never sets this column.
	 * 
	 * Note: If this column is not an auto_incremental int,
	 *       you MUST set this column in the record objects before an INSERT is performed
	 */
	var $id_col = 'id';
	
	/**
	 * Cache for the table field metadata.
	 * 
	 * Stores the names of the columns.
	 * If you define this by hand, you save one query
	 */
	var $columns;
	
	/**
	 * The classname of the classes produced by this factory.
	 * 
	 * Override normally to use another class than IR_record
	 * (preferably a descendant class)
	 */
	var $child_class = 'IR_record';
	
	/**
	 * Defines how the database should behave.
	 * 
	 * Packaged with IgnitedRecord by default: tree
	 * (list, rev (revision handling) are not finished)
	 * 
	 * Set to empty array (default) if to not use any special behaviour.
	 */
	var $act_as = array();
	
	/**
	 * Data for a Belongs To relationships.
	 * 
	 * There are two types of values that can be entered:
	 * 
	 * @arg Relation name, as a string or multiple in array
	 * @arg An array with this structure (can be put inside another array to enable multiple):
	 * @code
	 * var $belongs_to = array('name'  => 'relationname',
	 *                         'table' => 'tablename', // all others except this one can be omitted
	 *                         'fk'    => 'foreign_key_col'
	 *                        );
	 * @endcode
	 * 
	 * Default values are the following:
	 * @arg name => modelname or singular(tablename)
	 * @arg model => tablename or singular(tablename), if a model with that name exists
	 * @arg fk => tablename_id
	 * 
	 * @note The foreign key is stored in this table, with default column name othertablename_id
	 * @note Needs to be defined before constructor kicks in.
	 */
	var $belongs_to = array();
	
	/**
	 * Data for a Has Many relationships.
	 * 
	 * There are two types of values that can be entered:
	 * 
	 * @arg Relation name, as a string or multiple in array
	 * @arg An array with this structure (can be put inside another array to enable multiple):
	 * @code
	 * var $has_many = array('name'  => 'relationname', // all others except this one can be omitted
	 *                       'table' => 'tablename', // or this one
	 *                       'fk'    => 'foreign_key_col'
	 *                      );
	 * @endcode
	 * 
	 * Default values are the following:
	 * @arg name => tablename
	 * @arg model => tablename or singular(tablename), if a model with that name exists
	 * @arg fk => {$this->table}_id
	 * 
	 * @note The foreign key is stored in the other table, with default column name thistablename_id
	 * @note Needs to be defined before constructor kicks in.
	 */
	var $has_many = array();
	
	/**
	 * Data for a Has One relationships.
	 * 
	 * There are two types of values that can be entered:
	 * 
	 * @arg Relation name, as a string or multiple in array
	 * @arg An array with this structure (can be put inside another array to enable multiple):
	 * @code
	 * var $has_one = array('name'  => 'relationname', // all others except this one can be omitted
	 *                      'table' => 'tablename', // or this one
	 *                      'fk'    => 'foreign_key_col'
	 *                     );
	 * @endcode
	 * 
	 * Default values are the following:
	 * @arg name => modelname or singular(tablename)
	 * @arg model => tablename or singular(tablename), if a model with that name exists
	 * @arg fk => {$this->table}_id
	 * 
	 * @note The foreign key is stored in the other table, with default column name thistablename_id
	 * @note Needs to be defined before constructor kicks in.
	 */
	var $has_one = array();
	
	/**
	 * Data for a Has And Belongs To many relationships.
	 * 
	 * There are two types of values that can be entered:
	 * 
	 * @arg Relation name, as a string or multiple in array
	 * @arg An array with this structure (can be put inside another array to enable multiple):
	 * @code
	 * var $has_and_belongs_to_many = array('name'        => 'relationname', // all others except this one can be omitted
	 *                                        'table'       => 'tablename', // or this one
	 *                                        'join_table'  => 'relation_tablename'
	 *                                        'fk'          => 'foreign_key_col',
	 *                                        'related_fk'  => 'foreign_key_col'
	 *                                       );
	 * @endcode
	 * 
	 * Default values are the following:
	 * @arg name => tablename
	 * @arg join_table => tablename_{$this->table} or {$this->table}_tablename (arranged in alphabetical order)
	 * @arg fk => {$this->table}_id
	 * @arg related_fk => tablename_id
	 * @arg model => tablename or singular(tablename), if a model with that name exists
	 * 
	 * @note Needs to be defined before constructor kicks in.
	 */
	var $habtm = array();
	
	/**
	 * Alias for $habtm
	 */
	var $has_and_belongs_to_many = array();
	
	/**
	 * Set this to true to let dbobj2ORM() use fast object splitting when extracting data for this model.
	 * 
	 * It uses the prefix, which can result in collissions and data loss if it isn't carefully done.
	 */
	var $join_detect_w_prefix = false;
	
	/**
	 * Stores the relationship data
	 * 
	 * @access private
	 */
	var $relations = array();
	
	/**
	 * Child class helpers.
	 * 
	 * These are assigned to properties of the child class.
	 * They should be a class whose constructor takes a reference to the IR_record.
	 * 
	 * Is assigned by behaviours.
	 * key => propertyname, value => classname
	 * 
	 * @access private
	 * (accessed by Behaviours)
	 */
	var $child_class_helpers = array();
	
	/**
	 * Stores the names of the loaded behaviours
	 * 
	 * @access private
	 */
	var $loaded_behaviours = array();
	
	/**
	 * Stores all the registered hooks.
	 * 
	 * @access private
	 */
	var $hooks;
	
	/**
	 * The model name of this model.
	 * 
	 * Note: DO NOT ASSIGN, IgnitedRecord does this automatically
	 * @access private
	 */
	var $model_name;
	
	// --------------------------------------------------------------------
		
	/**
	 * Constructor.
	 * 
	 * @since 0.1.0 RC 1
	 * 
	 * Loads Behaviours.
	 * Sets the tablename if not already set and normalizes relationship data.
	 * Also loads the CodeIgniter inflector helper (if not loaded).
	 * 
	 * @param $settings An array containing the settings (optional)
	 */
	function IgnitedRecord($settings = array())
	{
		parent::IR_base();
		
		// init the model with the supplied settings
		foreach($settings as $key => $value)
		{
			$this->$key = $value;
		}
		
		// load inflector
		$CI =& IR_base::controller_instance();
		$CI->load->helper('inflector');
		
		$this->_load_behaviours($this->act_as);
		
		// set default classname if not already set
		if($this->table == null)
		{
			$class = get_class($this);
			
			// remove '_model' from classname if it exists
			if(strtolower(substr($class, -6)) == '_model')
				$class = substr($class, 0, -6);
			
			$this->table = plural($class);
		}
		
		// keep track of this model
		IR_base::add_model($this, $this->table);
		
		$this->_normalize_rel_properties();
		
		IR_base::log('debug', 'IgnitedRecord: Initialized model for table "'.$this->table.'".');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * To fool CI, letting it think this is a proper model.
	 * @access private
	 */
	function _assign_libraries(){}
	
	// --------------------------------------------------------------------
		
	/**
	 * Sets the class to produce.
	 * 
	 * Preferably descendants of IR_Record
	 * 
	 * @access public
	 * @param $class_name The class name of the objects to create
	 */
	function child_class($class_name)
	{
		$this->child_class = $class_name;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Specifies the settings for a Has Many relationship.
	 * 
	 * @since 0.2.0
	 * @access public
	 * @param $plural The tablename (or a plural relation name)
	 * @param $settings An array containing optional settings
	 * @return An IR_RelProperty_has object
	 */
	function &has_many($plural, $settings = array())
	{
		$obj = new IR_RelProperty_has($settings);
		$obj->plural($plural);
		$obj->parent_model =& $this;
		
		$this->relations[$plural] =& $obj;
		return $obj;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Specifies the settings for a Has One relationship.
	 * 
	 * @since 0.2.0
	 * @access public
	 * @param $singular The modelname (or a singular relation name)
	 * @param $settings An array containing optional settings
	 * @return An IR_RelProperty_has object
	 */
	function &has_one($singular, $settings = array())
	{
		$obj = new IR_RelProperty_has($settings);
		$obj->singular($singular);
		$obj->parent_model =& $this;
		
		$this->relations[$singular] =& $obj;
		return $obj;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Specifies the settings for a Belongs To relationship.
	 * 
	 * @since 0.2.0
	 * @access public
	 * @param $singular The modelname (or a singular relation name)
	 * @param $settings An array containing optional settings
	 * @return An IR_RelProperty object
	 */
	function &belongs_to($singular, $settings = array())
	{
		$obj = new IR_RelProperty($settings);
		$obj->singular($singular);
		
		$this->relations[$singular] =& $obj;
		return $obj;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Specifies the settings for a Has And Belongs To Many relationship.
	 * 
	 * @since 0.2.0
	 * @access public
	 * @param $plural The tablename (or a plural relation name)
	 * @param $settings An array containing optional settings
	 * @return An IR_RelProperty_habtm object
	 */
	function &habtm($plural, $settings = array())
	{
		$obj = new IR_RelProperty_habtm($settings);
		$obj->plural($plural);
		$obj->parent_model =& $this;
		$obj->parent_table = $this->table;
		
		$this->relations[$plural] =& $obj;
		return $obj;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Alias for habtm().
	 * 
	 * @since 0.2.0
	 * @access public
	 * @param $plural The tablename (or a plural relation name)
	 * @param $settings An array containing optional settings
	 * @return An IR_RelProp_habtm object
	 */
	function &has_and_belongs_to_many($plural, $settings = array())
	{
		return $this->habtm($plural, $settings);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Makes an on the fly loading of behaviours.
	 * 
	 * @since 0.2.0
	 * @access public
	 * @param $behaviour The behaviour(s) to load (accepts the same data as $act_as)
	 * @return void
	 */
	function act_as($behaviour)
	{
		$this->_load_behaviours($behaviour);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Adds a hook to the specified name and priority.
	 * 
	 * @access public
	 * @param $name The name of the hook (or an array of names)
	 * @param $function The function to be called, or the array(classobj, function) to be called
	 * @param $priority The priority of the function lower = higher priority
	 * @return void
	 */
	function add_hook($name, $function, $priority = 10)
	{
		foreach( (Array) $name as $hook)
		{
			$this->hooks[$name][$priority][] = $function;
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Runs the hook(s) registred with the name $name.
	 * 
	 * @access private
	 * @param $name The name of the hook to be called
	 * @param $data The array containing the data to pass on to registered functions
	 * @return true, or if any of the attached hooks want to abort, false
	 */
	function hook($name, $data = array())
	{
		// Have we got a hook for this specific event?
		if ( ! isset($this->hooks[$name]))
		{
			// No, do nothing
			return true;
		}
		else
		{
			// Yes, sort the list by priority
			ksort($this->hooks[$name]);
		}
		
		foreach($this->hooks[$name] as $priority => $functions)
		{
			if(is_array($functions))
			{
				foreach($functions as $func)
				{
					if(call_user_func_array($func, $data) === false)
						return false;  // abort directly
				}
			}
		}
		
		return true;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Creates a new model from the parameters sent.
	 * 
	 * Can be called statically.
	 * 
	 * @since 0.1.0 RC 2
	 * @access public
	 * @param $table The table the generated model will link to
	 * @param $settings The settings of this model 
	 *        (stored as propertyname as the key and value as the value)
	 * @return A new model object
	 */
	function &factory($table, $settings = array())
	{
		$settings['table'] = $table;
		$model = new IgnitedRecord($settings); // PHP 4: $model =& new IgnitedRecord($settings);
		return $model;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Creates a new model from a YAML file/string.
	 * 
	 * If the table field isn't stored in the YAML file, plural of the filename
	 * (before the first dot) will be used instead
	 * 
	 * Can be called statically.
	 *
	 * @since 0.1.1
	 * @access public
	 * @param $filename The filename or the yaml string
	 * @return An instance of IgnitedRecord instantiated with the parameters from the YAML data
	 */
	function &yamlfactory($filename)
	{
		$CI =& IR_base::controller_instance();
		// load the yaml parser
		$CI->load->helper('yayparser');
		$CI->load->helper('inflector');
		
		if(strpos($filename, "\n") !== false)
		{
			// parse (replace this with whatever parser you use)
			$array = yayparser($filename);
		}
		elseif(file_exists($filename))
		{
			$yaml = implode('', file($filename));
			
			// parse (replace this with whatever parser you use)
			$array = yayparser($yaml);
			
			if( ! isset($array['table']))
			{
				// remove file extension
				$segments = explode('.', basename($filename));
				
				// grab the first part, SQL does not allow dots anyway
				$array['table'] = plural(array_shift($segments));
			}
		}
		else
		{
			IR_base::show_error('IgnitedRecord: The yaml file "'.$filename.'" cannot be found.');
		}
		
		if( ! isset($array['table']))
		{
			IR_base::show_error('IgnitedRecord: YAMLfactory(): The table setting must be set.');
		}
		
		$model = new IgnitedRecord($array); // PHP 4: $model =& new IgnitedRecord($array);
		return $model;
	}
	
	//////////////////////////////////////////////
	//    Find methods
	//////////////////////////////////////////////
	
	/**
	 * Fetches an IR_record from the db.
	 * 
	 * @since 0.1.0 RC 2
	 * 
	 * To be used with CodeIgniter's ActiveRecord class to sort and filter the query.
	 * This method is equivalent to:
	 * @code
	 * $this->db->get($this->table, 1);
	 * @endcode
	 *
	 * @access public
	 * @return A populated IR_record if result is found, false otherwise
	 */
	function &get()
	{
		$this->hook('pre_get', array(&$this));
		
		$this->select($this->table.'.*');
		// limit to one row if there are no joins
		$query = parent::get($this->table, (! empty($this->joined_tables)) ? false : 1);
		
		$obj =& $this->dbobj2ORM($query, true);
		
		$this->hook('post_get', array(&$obj));
		return $obj;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Fetches the IR_record object with the id $id.
	 * 
	 * In the case with multiple primary keys you have to specify the values of
	 * all primary key columns. They must be in the same order as the values in
	 * $this->id_col.
	 *
	 * @access public
	 * @param $id The id of the row, if omitted it will act as an alias for get()
	 * @return A populated IR_record if result is found, false otherwise
	 */
	function &find($id = false)
	{
		// alias get() if we don't have an id
		if($id === false)
		{
			return $this->get();
		}
		
		$this->hook('pre_find', array(&$this, $id));
		
		// prefix with table name to avoid collitions
		$id_cols = array();
		foreach((Array)$this->id_col as $col)
		{
			$id_cols[] = $this->table.'.'.$col;
		}
		
		// build where array
		if(($where = $this->_array_combine($id_cols, (Array)$id)) === false)
		{
			IR_base::log('error', 'IgnitedRecord: Number of primary key columns and number of data argumnents does not match in call to find() for table '.$this->table.'.');
			$false = false;
			return $false;
		}
		
		// fetch
		$this->select($this->table.'.*');
		// limit to one row if there are no joins
		$query = parent::get_where($this->table, $where, ( ! empty($this->joined_tables)) ? false : 1);
		
		$obj =& $this->dbobj2ORM($query, true);
		
		$this->hook('post_find', array(&$obj, $id));
		return $obj;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Fetches the IR_record object where the $column contains $data.
	 * 
	 * This function merges the $column and $data into one array,
	 * where the $column becomes the key(s) in the array and
	 * the $data becomes the value(s), using _array_combine(). \n
	 * This array is then passed into CodeIgniter's db::get_where()
	 * method as the where clause.
	 * 
	 * @access public
	 * @param $column The column name(s) to match, typecasted to array if not already
	 * @param $data The data value(s) to match, typecasted to array if not already. If not set, $column is passed to IgnitedQuery::where()
	 * @return A populated IR_record if result is found, false otherwise
	 */
	function &find_by($column, $data = false)
	{
		if($data !== false)
		{
			if(($where = $this->_array_combine((Array)$column, (Array)$data)) === false)
			{
				IR_base::log('error', 'IgnitedRecord: Number of columns and number of data argumnents does not match in call to find_by().');
				$false = false;
				return $false;
			}
		}
		else
		{
			$where = $column;
		}
		
		$this->hook('pre_find_by', array(&$this, &$where));
		
		// fetch
		$this->select($this->table.'.*');
		// limit to one row if there are no joins
		$query = parent::get_where($this->table, $where, ( ! empty($this->joined_tables)) ? false : 1);
		
		$obj =& $this->dbobj2ORM($query, true);
		
		$this->hook('post_find_by', array(&$obj, $where));
		return $obj;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Fetches the IR_record object with the sql supplied.
	 * 
	 * @access public
	 * @param $sql The sql query, needs escaping of values (must start with SELECT * or equivalent)
	 * @param $binds An array of binding data
	 * @return A populated IR_record if a result is found, false otherwise
	 */
	function &find_by_sql($sql, $binds = array())
	{
		$this->hook('pre_find_by_sql', array(&$sql, &$binds));
		
		$query = $this->db->query($sql, $binds);
		if( ! $query->num_rows())
		{
			$false = false;
			return $false;
		}
		
		$obj =& $this->dbobj2ORM($query, true);
		
		$this->hook('post_find_by_sql', array(&$obj, $sql));
		return $obj;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Fetches all IR_record objects in the database.
	 * 
	 * @access public
	 * @return An array with populated IR_records, empty array if table is empty
	 */
	function &find_all()
	{
		$this->hook('pre_find_all', array(&$this));
		
		$this->select($this->table.'.*');
		
		$query = parent::get($this->table);
		
		$arr =& $this->dbobj2ORM($query);
		
		$this->hook('post_find_all', array(&$arr));
		return $arr;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Fetches all IR_record objects where the $column contains $data.
	 * 
	 * This function merges the $column and $data into one array,
	 * where the $column becomes the key(s) in the array and
	 * the $data becomes the value(s), using _array_combine(). \n
	 * This array is then passed into CodeIgniter's db::get_where()
	 * method as the where clause.
	 * 
	 * @access public
	 * @param $column The column name(s) to match
	 * @param $data The data which will be used to match, if not set $column will be sent to IgnitedQuery::where()
	 * @return An array with populated IR_records if results are found, empty array otherwise
	 */
	function &find_all_by($column, $data = false)
	{
		$arr = array();
		
		if($data !== false)
		{
			if(($where = $this->_array_combine((Array)$column, (Array)$data)) === false)
			{
				IR_base::log('error', 'IgnitedRecord: Number of columns and number of data argumnents does not match in call to find_by().');
				return $arr;
			}
		}
		else
		{
			$where = $column;
		}
		
		$this->hook('pre_find_by', array(&$this, &$where));
		
		$this->select($this->table.'.*');
		$query = parent::get_where($this->table, $where);
		
		$arr = $this->dbobj2ORM($query);
		
		$this->hook('post_find_all_by', array(&$arr, $where));
		return $arr;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Fetches all IR_record objects which match the sql supplied.
	 * 
	 * @access public
	 * @param $sql The sql query, needs escaping of values (must start with SELECT * or equivalent)
	 * @param $binds An array of binding data
	 * @return An array with populated IR_records if resultas are found, false otherwise
	 */
	function &find_all_by_sql($sql, $binds = array())
	{
		$this->hook('pre_find_by_sql', array(&$sql, &$binds));
		
		$query = $this->db->query($sql, $binds);
		$arr = array();
		
		$arr = $this->dbobj2ORM($query);
		
		$this->hook('post_find_all_by_sql', array(&$arr, $sql));
		return $arr;
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
		$this->hook('pre_count', array(&$this));
		
		$this->select('COUNT(1)', false);
		$query = parent::get($this->table);
		
		$count = array_shift($query->row_array());
		$query->free_result();
		
		$this->hook('post_count', array(&$count));
		
		return $count;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Creates a new empty IR_record object.
	 * 
	 * @access public
	 * @param $data The data of the new object, is assigned to the new object before it is returned
	 * @return A new IR_record
	 */
	function &new_record($data = array())
	{
		$class = $this->child_class;
		$obj = new $class($this, $data); // PHP 4: $obj =& new $class($this, $data);
		
		foreach($this->child_class_helpers as $name => $hclass)
		{
			$obj->$name = new $hclass($obj); // PHP 4: $obj->$name =& new $hclass($obj);
		}
		
		$this->hook('post_new_record', array(&$obj));
		return $obj;
	}
	
	//////////////////////////////////////////////
	//    Save, delete and update methods
	//////////////////////////////////////////////
		
	/**
	 * Saves the supplied object in the databse.
	 * 
	 * Takes a reference of the object (edits the object after insert)'
	 * 
	 * Note: Can only save the $object if it belongs to the same table as this model
	 * 
	 * @access public
	 * @param $object The object to be inserted or updated, passed by reference
	 * @param $force If to force the save, to save even unchanged objects
	 * @return true if the record was saved, false otherwise
	 */
	function save(&$object, $force = false)
	{
		if($this->table == $object->__table)
		{
			if($this->hook('save_pre_strip_data', array(&$object)) === false)
			{
				IR_base::log('debug', 'IgnitedRecord: save() method didn\'t save object, the attached hooks aborted saving on hook "save_pre_strip_data".');
				$this->hook('save_abort', array(&$object));
				return false; // hooks failed
			}
			
			// remove some values that shall not belong in the database
			$data =& $this->_strip_data($object);
			$no_escape = $this->_strip_data($object->__no_escape_data);
			
			// just update the data which have been changed
			$data = array_diff_assoc($data, $object->__data);
			
			$this->hook('save_post_strip_data', array(&$data, &$no_escape));
			
			if(empty($data) && empty($no_escape))
			{
				IR_base::log('error', 'IgnitedRecord:save() method didn\'t save object, the object hasn\'t been edited or is empty.');
				$this->hook('save_abort', array(&$object));
				return false;
			}
			
			// create query object
			$query = new IgnitedQuery();
			
			// save the non escaped data
			$query->set($no_escape, null, false);
			
			// restet unescape data
			$object->__no_escape_data = array();
			
			// check if row exists
			if($object->__id == null)
			{
				// no row exists in database, insert
				$this->hook('save_pre_insert', array(&$data));
				
				$ret = $query->insert($this->table, $data);
				$ret = ($ret && $query->affected_rows() > 0);
				
				$object->__id = array();
				foreach((Array)$this->id_col as $prop)
				{
					if(isset($data[$prop]))
						$object->__id[] = $data[$prop];
					else
						$object->__id[] = $query->insert_id(); // grabs the id of the inserted row
				}
				
				// Currently a dirty fix for relations:
				if(count($object->__id) == 1)
					$object->__id = $object->__id[0];
				
				$this->hook('save_post_insert', array(&$object));
			}
			else
			{
				// remove the ID properties
				foreach((Array)$this->id_col as $prop)
				{
					unset($data[$prop]);
				}
				
				// update
				$this->hook('save_pre_update', array(&$data));
				
				if(($where = $this->_array_combine((Array)$this->id_col, (Array)$object->__id)) === false)
				{
					IR_base::log('error', 'IgnitedRecord: Number of primary columns and number of data argumnents does not match in save() for table '.$this->table.'.');
					$false = false;
					return $false;
				}
				
				$query->where($where);
				$ret = $query->update($this->table, $data);
				$ret = ($ret && $query->affected_rows() > 0);
				
				$this->hook('save_post_update', array(&$object));
			}
			$this->hook('post_save', array(&$object));
			return $ret;
		}
		else
		{
			IR_base::show_error('Incompatible object supplied to '.classname($this).', tables does not match');
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Updates the data in the supplied object.
	 * 
	 * If the object does not exist in the database,
	 * the object will still contain all data.
	 * (but the internal id will be stripped)
	 * 
	 * @note Can only perform an update if the $object belongs to the same table as this instance
	 * 
	 * @access public
	 * @param $object The object to be updated, passed by reference
	 * 
	 * @todo Add abort hook and make it fire if a hooked function wants to abort (like save())
	 */
	function update(&$object)
	{
		if($this->table == $object->__instance->table)
		{
			// check if row exists
			if($object->in_db())
			{
				if($this->hook('pre_update', array(&$object)) === false)
					return false; // hooks failed
				
				if(($where = $this->_array_combine((Array)$this->id_col, (Array)$object->__id)) === false)
				{
					IR_base::log('error', 'IgnitedRecord: Number of primary columns and number of data arguments does not match in update() for table '.$this->table.'.');
					$false = false;
					return $false;
				}
				
				$q = new IgnitedQuery();
				
				$query = $q->get_where($this->table, $where, 1);
				
				if($query->num_rows())
				{
					$object =& $this->dbobj2ORM($query);
				}
				else
				{
					$object->__id = null;
				}
				
				$this->hook('post_update', array(&$object));
			}
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Deletes the $object from the database if it exists in database.
	 * 
	 * Also clears all relations with other rows.
	 * 
	 * @access public
	 * @param $object The IR_record to remove from database
	 * 
	 * @todo Add abort hook and make it fire if a hooked function wants to abort (like save())
	 * @todo check if it is possible to do a delete that cascades over multiple levels,
	 * this works ONLY one level
	 */
	function delete(&$object)
	{
		if($object->in_db())
		{
			if($this->hook('pre_delete', array(&$object)) === false)
				return false; // hooks failed
			
			$q = new IgnitedQuery();
			
			foreach($this->relations as $name => $data)
			{
				if($data->get_cascade_on_delete())
				{
					$data->build_query($object, $q);
					$q->delete();
				}
				elseif(is_a($data, 'IR_RelProperty_has') && ! is_a($data, 'IR_RelProperty_habtm'))
				{
					$data->build_query($object, $q);
					$q->set($data->get_fk(), null);
					$q->update();
				}
				
				if(is_a($data, 'IR_RelProperty_habtm'))
				{
					$q->delete($data->get_join_table(), array($data->get_fk() => $object->__id));
				}
			}
			
			$this->hook('pre_delete_query', array(&$object));
			if(($where = $this->_array_combine((Array)$this->id_col, (Array)$object->__id)) === false)
			{
				IR_base::log('error', 'IgnitedRecord: Number of primary columns and number of data arguments does not match in delete() for table '.$this->table.'.');
				$false = false;
				return $false;
			}
			
			$q->delete($this->table, $where);
			unset($object->__id);
			
			$this->hook('post_delete', array(&$object));
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Removes all data from the associated table, and also all relationships.
	 * 
	 * Attention: All Data Will Be Lost! Including all relations with other tables!
	 *            Not the related objects, though.
	 * 
	 * If a relations cascade_on_delete setting is on, the related objects will
	 * also be deleted for that relation.
	 * 
	 * @access public
	 */
	function delete_all()
	{
		$all =& $this->find_all();
		
		$q = new IgnitedQuery();
		
		foreach($this->relations as $name => $data)
		{
			if($data->get_cascade_on_delete())
			{
				foreach($all as $object)
				{
					$data->build_query($object, $q);
					$q->delete();
				}
			}
		}
		
		$q->empty_table($this->table);
		
		// unlink relations
		foreach($this->relations as $name => $data)
		{
			if(is_a($data, 'IR_RelProperty_habtm'))
			{
				$q->delete($data->get_join_table(), array($data->get_fk() => $object->__id));
			}
			
			if($data->get_cascade_on_delete())
			{
				continue;
			}
			elseif(is_a($data, 'IR_RelProperty_has') && ! is_a($data, 'IR_RelProperty_habtm'))
			{
				$data->build_query($object, $q);
				$q->set($data->get_fk(), null);
				$q->update();
			}
		}
	}
	
	//////////////////////////////////////////////
	//    Relationship methods
	//////////////////////////////////////////////
	
	/**
	 * JOINs the related object(s) into the current query in construction.
	 * 
	 * They are prefixed with their relation name.
	 * The sql where will only find rows with a related row attached to them.
	 * 
	 * Note: It is only possible to join Has One or Belongs To relationships.
	 * 
	 * @access public
	 * @param $name The relation name
	 * @param $columns The columns to fetch, default: all
	 * @param $type What type of join which should be used, default: left
	 * @return $this
	 */
	function join_related($name, $columns = false, $type = 'left')
	{
		if(isset($this->relations[$name]))
		{
			$this->relations[$name]->join($this, $name, $this, $columns, $type);
			
			if( ! $columns)
				$this->joined_tables[$name] = array('table' => $this->relations[$name]->get_table(),
													'single' => ! $this->relations[$name]->plural);
		}
		else
		{
			IR_base::log('error', 'IgnitedRecord: Relation "'.$name.'" was not found.');
		}
		
		return $this;
	}
	
	// These methods below are called by objects this class creates,
	// so I recommend that you only call these methods if you know what you are doing.
	// Please use the methods in IR_record instead
	
	// --------------------------------------------------------------------
		
	/**
	 * Creates a relation query object matching the provided relation name.
	 * 
	 * If no relation name is found, a relation query object is returned,
	 * this object returns false upon get().
	 * 
	 * @access private
	 * (accessed by child objects)
	 * @param $object	The child object
	 * @param $name		The relation name
	 */
	function &load_rel(&$object, $name)
	{
		$rel_query = new IR_RelQuery(); // PHP 4: $rel_query =& new IR_RelQuery()
		
		if($object->__table == $this->table && $object->in_db())
		{
			if(isset($this->relations[$name]))
			{
				return $this->relations[$name]->build_relquery($object, $rel_query);
			}
		}
		
		$rel_query->no_rel = true;
		return $rel_query;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Establishes a relationship between the two supplied objects.
	 * 
	 * Determines which method that shall be used; Belongs To, Has Many, Has One or Has And Belongs To Many. \n
	 * Performs some checking of input
	 * 
	 * @param $child An IR_record created by this IgnitedRecord model
	 * @param $object Another IR_record (must have an instance tied to it)
	 * @param $rel_name The name of the relationship, leave blank if to auto determine
	 * @return true if the establishment of a relation succeeded, false otherwise
	 */
	function establish_relationship(&$child, &$object, $rel_name = false, $attributes = array())
	{
		if( ! isset($child->__instance) || $child->__instance->table != $this->table)
		{
			IR_base::log('error', 'An incompatible object was supplied to an IgnitedRecord object belonging to the table '. $child->__table);
			return false;
		}
		
		if( ! isset($object->__instance)){
			IR_base::log('error', 'An incompatible object was supplied to an IgnitedRecord object belonging to the table '. $object->__table);
			return false;
		}
		
		// if we don't have a name, find it
		if(empty($rel_name)){
			foreach($this->relations as $name => $obj)
			{
				if($obj->get_table() == $object->__table)
				{
					$rel_name = $name;
					break; // Jump happily because we've found it!!! :P
				}
			}
		}
		
		if(isset($this->relations[$rel_name]))
			return $this->relations[$rel_name]->establish($child, $object, $attributes);
		
		IR_base::log('error', "IgnitedRecord: no relation with the relationname $rel_name was found, no relation established.");
		return false;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Removes the relationship between the two supplied objects if it exists.
	 * 
	 * Determines which method that shall be used; Belongs To, Has Many, Has One or Has And Belongs To Many. \n
	 * Performs some checking of input
	 * 
	 * @access private
	 * @param $child An IR_record created by this IgnitedRecord model
	 * @param $object Another IR_record (must have an instance tied to it)
	 * @param $rel_name The name of the relationship, leave blank if to auto determine
	 */
	function remove_relationship(&$child, &$object, $rel_name = false){
		if( ! isset($child->__instance) || $child->__instance->table != $this->table){
			IR_base::log('error', 'An incompatible object was supplied to an IgnitedRecord object belonging to the table '
								  .$child->__table);
			return false;
		}
		if( ! isset($object->__instance)){
			IR_base::log('error', 'An incompatible object was supplied to an IgnitedRecord object belonging to the table '
								  .$object->__table);
			return false;
		}
		
		// if we don't have a name, find it
		if(empty($rel_name)){
			foreach($this->relations as $name => $obj)
			{
				if($obj->get_table() == $object->__table)
				{
					$rel_name = $name;
					break; // Jump happily because we've found it!!! :P
				}
			}
		}
		
		if(isset($this->relations[$rel_name]))
			return $this->relations[$rel_name]->destroy($child, $object);
		
		IR_base::log('error', 'IgnitedRecord: no relation with the relationname '.
						     $rel_name.' was found, no relation removed.');
	}
	
	//////////////////////////////////////////////
	//    Private methods
	//////////////////////////////////////////////
		
	/**
	 * Aggregates the behaviours into this object, only PHP 5.
	 * 
	 * In PHP 4, this method won't aggregate the behaviours,
	 * so you have to directly call the behaviour.
	 * 
	 * @code
	 * $object->behaviourname->method();
	 * // instead of
	 * $object->method();
	 * @endcode
	 * 
	 * @param $method The method called
	 * @param $args The argument array sent to the method
	 * @return Whatever the helper method returns
	 */
	function __call($method, $args)
	{
		foreach($this->loaded_behaviours as $property)
		{
			if(method_exists($this->$property, $method))
				return call_user_func_array(array($this->$property, $method), $args);
		}
		
		$trace = debug_backtrace();
		
		IR_base::show_error("IgnitedRecord: Method $method is not found.<br />\nCalled on line {$trace[1]['line']} in {$trace[1]['file']}.");
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Fetches all data properties from an object.
	 * 
	 * Skips the relation properties, the child helpers
	 * instance variables (tied to IgnitedRecord and other classes)
	 * and also the id column (all properties which are not table columns).
	 * 
	 * @access private
	 * @param $object The object to be cleaned, or an array
	 * @return An associative array containing the data from the object,
	 * key is property name and value is value of the property
	 */
	function &_strip_data(&$object)
	{
		$data = array();
		
		if(is_object($object))
		{
			foreach(IR_base::list_fields($this->table) as $col)
			{
				if(isset($object->$col))
					$data[$col] = $object->$col;
			}
		}
		else
		{
			foreach(IR_base::list_fields($this->table) as $col)
			{
				if(isset($object[$col]))
					$data[$col] = $object[$col];
			}
		}
		
		return $data;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Loads the behaviours specified in $act_as.
	 * 
	 * Expects the behaviours to be localized in files which lie in the subfolder
	 * behaviours/ with the names like:
	 * behaviourname.php
	 * 
	 * and to have the class name linke this:
	 * IgnitedRecord_behaviourname
	 * 
	 * If the behaviour class already exists, no loading of files takes place.
	 * The behaviourname is always lowercase
	 * 
	 * @access private
	 * @param $list The list of behaviours to load
	 */
	function _load_behaviours($list)
	{
		foreach((Array) $list as $key => $act)
		{
			if( ! is_numeric($key))
			{
				$opt = $act;
				$act = $key;
			}
			else
			{
				$opt = array();
			}
			
			$act = strtolower($act);
			$exists = false;
			$class_name = 'IgnitedRecord_'.$act;
			
			// is loaded?, if not, try to load
			if( ! class_exists($class_name))
			{
				$path = dirname(__FILE__);
				
				if(file_exists($path.'/behaviours/'.$act.'.php'))
				{
					include_once($path.'/behaviours/'.$act.'.php');
					if(class_exists($class_name))
						$exists = true;
				}
				else
				{
					IR_base::log('error', 'IgnitedRecord: Behaviour file '.$act.
										  '.php does not exists in the behaviours dir. Cannot load '.$act.'.');
				}
			}
			else
			{
				$exists = true;
			}
			
			if($exists == true)
			{
				// Check if a reference to a library, model or anything else
				// exists in $this,
				if(isset($this->$act))
				{
					// unset removes reference, prevents overwrite of the original data
					unset($this->$act);
				}
				
				// load behaviour
				$this->$act = new $class_name($this, $opt); // PHP 4: $this->$act =& new $class_name($this, $opt);
				$this->loaded_behaviours[] = $act;
			}
			else
			{
				IR_base::log('error', 'IgnitedRecord: Behaviour class IgnitedRecord_'.$act.
									  ' does not exists. Cannot load '.$act.'.');
			}
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Normalizes the data found in the relation properties,
	 * and creates IR_RelProperty objects for them.
	 */
	function _normalize_rel_properties()
	{
		$types = array('has_many' => true,
					   'has_one' => false,
					   'belongs_to' => false,
					   'habtm' => true,
					   'has_and_belongs_to_many' => true);
		
		foreach($types as $rel => $plural)
		{
			// if there is no data, skip here for speed
			if(empty($this->$rel))
			{
				$this->$rel = array(); // reset, just in case
				continue;
			}
			
			// only a string, then make it the relation name
			if(is_string($this->$rel))
			{
				$this->$rel = array('name' => $this->$rel);
			}
			
			// is it a single relation? then encapsulate it in an array
			$var =& $this->$rel;
			if(isset($var['table']) OR isset($var['name']))
			{
				$this->$rel = array($this->$rel);
			}
			
			$tmp = $this->$rel;
			unset($this->$rel);
			
			foreach($tmp as $name => $data)
			{
				// $name may be a relationship name, which makes for shorter syntax
				if( ! is_numeric($name))
				{
					if(is_string($data))
					{
						$data = array('table' => $data);
					}
					
					$data['name'] = $name;
				}
				
				if(is_string($data))
				{
					$data = array('name' => $data);
				}
				
				if( ! (isset($data['name']) OR isset($data['table'])))
				{
					IR_base::show_error('IgnitedRecord: No tablename and/or relation name was specified when defining a relation of type "'.
							$rel.'".');
				}
				
				// get the right relation name
				if(isset($data['name']))
				{
					// if we have a name, use it
					$name = $data['name'];
				}
				elseif($plural)
				{
					// we must have a name or a table, then table is plural and suitable
					$name = $data['table'];
				}
				else
				{
					// nope, use singular of tablename
					$name = singular($data['table']);
				}
				
				// call the relation specifying function
				$this->$rel($name, $data);
			}
		} // end foreach($types)
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the modelname of this object.
	 * 
	 * Which is classname or the name of the CI property this object lies in.
	 * 
	 * @access private
	 * (accessed by IR_RelProperty)
	 * @return string, or false if not found
	 */
	function _get_modelname()
	{
		// check cache
		if(isset($this->model_name))
		{
			return $this->model_name;
		}
		
		$class = strtolower(get_class($this));
		
		// check if it is a user defined class
		if($class == 'ignitedrecord')
		{
			// no, set default and then iterate over the CI properties
			$class = singular($this->table);
			$CI =& IR_base::controller_instance();
		
			if(floor(phpversion()) < 5)
			{
				// PHP 4, serialize it here, to gain performance
				$serialized = serialize($this);
			
				foreach(array_keys(get_object_vars($CI)) as $key)
				{
					// PHP 4 cannot handle self-referencing properties,
					// use serialize to determine if they are identical
					if(serialize($CI->$key) === $serialized)
					{
						$class = $key;
						break;
					}
				}
			}
			else
			{
				// PHP 5+
				foreach(array_keys(get_object_vars($CI)) as $key)
				{
					// PHP 5 can compare objects with self-referencing properties
					if($CI->$key === $this)
					{
						$class = $key;
						break;
					}
				}
			}
		}
		
		// remove '_model' from classname if it exists
		if(strtolower(substr($class, -6)) == '_model')
			$class = substr($class, 0, -6);
		
		if($class == 'ignitedrecord')
		{
			$class = false;
		}
		
		$this->model_name = $class;
		return $class;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Combines two non asociative arrays to one asociative.
	 * 
	 * Exists in PHP 5, but not in 4
	 * 
	 * @access private
	 * @param $keys The array with the keys
	 * @param $values The array with the values
	 * @return An asociative array with the keys from $keys and the values from $values
	 */
	function _array_combine($keys, $values)
	{
		if(function_exists('array_combine'))
			return array_combine($keys, $values); // PHP 5
		
		// This code below is from the PEAR PHP_Compat package
		// It is licensed under the PHP license,
		// see http://www.php.net/license/3_01.txt
		// Copyright (c) 1999 - 2008 The PHP Group. All rights reserved.
		if ( ! is_array($keys) ||
			 ! is_array($values) ||
			count($keys) !== count($values) ||
			empty($keys) || empty($values))
		{
			return false;
		}
		
		$keys	= array_values($keys);
		$values = array_values($values);
		$combined = array();
		
		for ($i = 0, $cnt = count($values); $i < $cnt; $i++) {
			$combined[$keys[$i]] = $values[$i];
		}
		
		return $combined;
		// end code from PHP_Compat
	}
}


// --------------------------------------------------------------------
	
/**
 * Dumps the record passed as first parameter.
 * 
 * @param $var The value to be dumped
 * @param $i_char The character(s) used to indent one step
 * @param $br The character(s) used to break a row
 * @param $level The current indent level (used in recursion)
 * @param $ref_chain A chain of parent objects
 * @return void
 */
function idump($var, $i_char = '    ', $br = "\n", $level = 0, $ref_chain = array())
{
	$skip = array('__instance', '__data', '__table', '__id', '__no_escape_data');
	
	// skip IgnitedRecord instances, to avoid circular references from behaviours
	if(is_object($var) && is_a($var, 'IgnitedRecord'))
	{
		echo 'IgnitedRecord model "'.$var->_get_modelname().'"';
		return;
	}
	
	// check if we've prinited this before
	$s_var = serialize($var);
	
	foreach($ref_chain as $ref)
	{
		if(serialize($ref) == $s_var)
		{
			echo "-RECURSION-";
			return;
		}
	}
	
	// add this var, to avoid it to be printed with circular references
	$ref_chain[] =& $var;
	
	if(is_object($var))
	{
		echo get_class($var);
		
		if(is_a($var, 'IR_record'))
		{
			// extra info
			echo ' linked to '.$var->__table.(( ! empty($var->__id)) ? '('.implode(', ', (Array)$var->__id).')' : '').', model: ';
			
			// get the modelname and print it
			if(isset($var->__instance))
			{
				echo '"'.$var->__instance->_get_modelname().'"';
			}
			else
			{
				echo "none";
			}
			
			echo $br.str_repeat($i_char, $level).'{'.$br;
			
			// print all vars except for internal ones
			foreach(get_object_vars($var) as $prop => $val)
			{
				if(in_array($prop, $skip))
					continue;
				
				echo str_repeat($i_char, $level + 1)."[$prop".(($var->is_changed($prop) && ! is_object($val)) ? '*' : ']')." => ";
				
				idump($val, $i_char, $br, $level + 1, $ref_chain);
				
				echo $br;
			}
		}
		else
		{
			// normal object
			
			echo $br.str_repeat($i_char, $level).'{'.$br;
			
			// print all vars
			foreach(get_object_vars($var) as $prop => $val)
			{
				echo str_repeat($i_char, $level + 1)."[$prop] => ";
				
				idump($val, $i_char, $br, $level + 1, $ref_chain);
				
				echo $br;
			}
		}
		
		echo str_repeat($i_char, $level).'}';
	}
	elseif(is_array($var))
	{
		echo 'Array';
		
		echo $br.str_repeat($i_char, $level).'{'.$br;
		
		foreach($var as $prop => $val)
		{
			echo str_repeat($i_char, $level + 1)."[$prop] => ";
			
			idump($val, $i_char, $br, $level + 1, $ref_chain);
			
			echo $br;
		}
		
		echo str_repeat($i_char, $level).'}';
	}
	else
	{
		echo gettype($var).(is_string($var) ? '('.strlen($var).')' : '').' '.(is_string($var) ? "\"$var\"" : $var);
	}
}

/**
 * @}
 */
/* End of file ignitedrecord.php */
/* Location: ./application/libraries/ignitedrecord/ignitedrecord.php */
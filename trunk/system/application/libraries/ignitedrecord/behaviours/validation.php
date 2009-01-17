<?php 
/*
 * Created on 2008 Aug 30
 * by Martin Wernstahl <m4rw3r@gmail.com>
 */


// example settings:

/*	
array('fieldname' => array(
		'label' => 'somename',						// the written label (move to language file)
		'rules' => 'required',						// validation rules
		'type' => 'text',							// the form field type
		'attr' => array('size' => 100),				// the form field attributes
		'default' => 'Type your search string here',// the default value
		'desc' => 'The search string'				// the description of the field (move to language file)
						)
	);*/



/**
 * 
 */
class IgnitedRecord_validation{
	
	/**
	 * Parent model.
	 * 
	 * @access private
	 */
	var $parent;
	
	/**
	 * Final error array.
	 * 
	 * @access private
	 */
	var $errors = array();
	
	/**
	 * Temporary errors-
	 * 
	 * @access private
	 */
	var $_errors = array();
	
	/**
	 * Stores the name of the rule currently run, to be able to place the error accordingly.
	 * 
	 * @access private
	 */
	var $_current_rule;
	
	/**
	 * Stores the name of the field currently run.
	 * 
	 * @access private
	 */
	var $_current_field;
	
	/**
	 * Stores the object which is currently validated.
	 * 
	 * @access private
	 */
	var $_current_object;
	
	function IgnitedRecord_validation(&$parent, $opts)
	{
		$this->settings = $opts;
		$this->parent =& $parent;
		$this->CI =& get_instance();
		
		$parent->child_class_helpers['validation'] = 'IR_validation_helper';
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Validates $object.
	 * 
	 * If the object is unchanged or the fields to validate not populated
	 * This method will return false without running validation.
	 * 
	 * This method skips validation of unchanged fields, hence making it easier to reuse validation.
	 * 
	 * @return bool
	 */
	function validate(&$object)
	{
		// reset
		$result = true;
		$this->errors = $this->_errors = array();
		
		// check if object is empty or unchanged, just return false if it is
		$count = 0;
		foreach($this->settings as $name => $data)
		{
			if($object->is_changed($name))
				$count++;
		}
		
		if( ! $count)
		{
			// empty object, return false
			return false;
		}
		
		$this->_current_object =& $object;
		$this->CI->lang->load('IR_validation');
		
		// loop validation rules
		foreach($this->settings as $name => $data)
		{
			if(isset($data['rules']))
			{
				if( ! $object->is_changed($name) && strpos($data['rules'], 'matches[') === false)
				{
					// unchanged
					continue;
				}
				
				$curr_result = true;
				$rules = explode('|', $data['rules']);
				$this->_current_field = $name;
				
				foreach($rules as $rule)
				{
					// if not a callback and we have an error, quit
					if($curr_result == false && strpos($rule, 'callback_') === false)
					{
						continue;
					}
					
					// determine if it is a callback
					$callback = false;
					if(strpos($rule, 'callback_') === 0)
					{
						$callback = true;
						$rule = substr($rule, 9);
					}
					
					// set which rule we're working on, so we and add_error() is on the same page
					$this->_current_rule = $rule;
					
					// fix the data, to aviod errors
					$args = array(isset($object->$name) ? $object->$name : null);
					
					// get parameter (zero isn't acceptable)
					if(($start = strpos($rule, '[')) != false && ($end = strpos($rule, ']')) != false)
					{
						// we've got a parameter, extract it
						$args[] = $param = substr($rule, $start + 1, $end - $start - 1);
						$rule = substr($rule, 0, $start);
					}
					else
					{
						$param = false;
					}
					
					// run rule
					
					// this object
					if( ! $callback && method_exists($this, '_'.$rule))
					{
						$call = array(&$this, '_'.$rule);
					}
					// Model
					elseif(method_exists($this->parent, $rule))
					{
						$call = array(&$this->parent, $rule);
					}
					// Controller
					elseif(method_exists($this->CI, $rule))
					{
						$call = array(&$this->CI, $rule);
					}
					// Callable (function or static method)
					elseif(is_callable($rule))
					{
						$call = $rule;
					}
					else
					{
						show_error('The validation rule "'.$rule.'" was not found.');
					}
					
					$ret = call_user_func_array($call, $args);
					
					// check the return
					if( ! is_bool($ret))
					{
						$object->$name = $ret;
					}
					elseif($ret == false)
					{
						if( ! isset($this->_errors[$rule]) && ($line = $this->CI->lang->line($rule)) === false)
						{
							$line = 'Unable to load error message for rule "'.$rule.'".';
						}
						elseif(isset($this->_errors[$rule]))
						{
							$line = $this->_errors[$rule];
						}
						
						// fix the string
						$name = isset($data['label']) ? $data['label'] : $name;
						$this->errors[] = sprintf($line, $name, $param);
						
						$curr_result = false;
					}
				}
				// End foreach($rules)
				
				$result = $curr_result && $result;
			}
			else
			{
				// TODO: Log error
			}
		}
		// End foreach($settings)
		
		$object->validation->errors = $this->errors;
		
		return $result;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Adds an error sting, used by callbacks
	 */
	function add_error($string)
	{
		$this->_errors[$this->_current_rule] = $string;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * 
	 */
	function errors()
	{
		return $this->errors;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Generates a form with the default field values wich are overridden by $data.
	 */
	function form($settings = array())
	{
		// action
		// data (the object to fetch defaults from)
		// skip The fields to be skipped
		// fields Extra fields, ie upload
		
		// sent through the form_attr value
		// enctype
		// class
		// id
		
		$this->CI->load->helper('form');
		
		// merge in defaults
		$default = array('action'				=> current_url(),
						 'form_attr'			=> '',
						 'button_text'			=> 'Submit!',
						 'data'					=> array(),
						 'fields'				=> array(),
						 'errors'				=> array(),
						 'skip'					=> array(),
						 'field_size'			=> '',
						 'display_tips'			=> true,
						 'required_fields_text' => 'Required Fields');
		
		$settings = array_merge($default, $settings);
		
		// fields
		$fields = array_merge($this->settings, $settings['fields']);
		
		// load field data from $settings['data']
		foreach($fields as $name => $data)
		{
			if(is_array($settings['data']))
			{
				if(isset($settings['data'][$name]))
				{
					$fields[$name]['default'] = $settings['data'][$name];
				}
			}
			elseif(is_object($settings['data']))
			{
				if(isset($settings['data']->$name))
				{
					$fields[$name]['default'] = $settings['data']->$name;
				}
			}
		}
		
		// remove the original data object
		unset($settings['data']);
		
		// set defaults
		foreach($fields as $name => $data)
		{
			// merge defaults
			$fields[$name] = array_merge(array('label' => $name,
					'rules'=> '',
					'type' => 'text',
					'attr' => array(),
					'default' => '',
					'desc' => ''), $data);
		}
		
		// set errors and field data
		$settings['fields'] = $fields;
		$settings['errors'] = array_merge($settings['errors'], $this->errors());
		
		// remove fields to skip
		foreach( (Array) $settings['skip'] as $toskip)
		{
			if(isset($settings['fields'][$toskip]))
				unset($settings['fields'][$toskip]);
		}
		
		unset($settings['skip']);
		
		// render view
		return $this->CI->load->view('IR_form', $settings, true);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns true if the string supplied is unique.
	 * 
	 * @param $str The string to compare
	 * @return bool
	 */
	function _unique($str)
	{
		$q = new IgnitedQuery();
		
		$q->select('1', false);
		$q->from($this->parent->table);
		$q->where($this->_current_field, $str);
		$q->limit(1);
		
		$r = $q->get();
		
		if($r->num_rows())
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * 
	 */
	function _required($str)
	{
		if ( ! is_array($str))
		{
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			return ( ! empty($str));
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	function _matches($str, $field)
	{
		if ( ! isset($this->_current_object->$field))
		{
			return FALSE;				
		}
		
		$field = $this->_current_object->$field;

		return ($str !== $field) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Minimum Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	function _min_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}
	
		return (strlen($str) < $val) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Max Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */	
	function _max_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}
	
		return (strlen($str) > $val) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Exact Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */	
	function _exact_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}
	
		return (strlen($str) != $val) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Valid Email
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function _valid_email($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Valid Emails
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function _valid_emails($str)
	{
		if (strpos($str, ',') === FALSE)
		{
			return $this->_valid_email(trim($str));
		}
		
		foreach(explode(',', $str) as $email)
		{
			if (trim($email) != '' && $this->_valid_email(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Validate IP Address
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function _valid_ip($ip)
	{
		return $this->CI->input->valid_ip($ip);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */		
	function _alpha($str)
	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Alpha-numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function _alpha_numeric($str)
	{
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function _alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function _numeric($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

	}

	// --------------------------------------------------------------------

	/**
	 * Is Numeric
	 *
	 * @access    public
	 * @param    string
	 * @return    bool
	 */
	function _is_numeric($str)
	{
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	} 

	// --------------------------------------------------------------------
	
	/**
	 * Integer
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */	
	function _integer($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]+$/', $str);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function _is_natural($str)
	{   
		return (bool)preg_match( '/^[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function _is_natural_no_zero($str)
	{
		if ( ! preg_match( '/^[0-9]+$/', $str))
		{
			return FALSE;
		}
		
		if ($str == 0)
		{
			return FALSE;
		}
	
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Valid Base64
	 *
	 * Tests a string for characters outside of the Base64 alphabet
	 * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function _valid_base64($str)
	{
		return (bool) ! preg_match('/[^a-zA-Z0-9\/\+=]/', $str);
	}
}

/**
 * 
 */
class IR_validation_helper{
	
	/**
	 * The errors.
	 */
	var $errors = array();
	
	function IR_validation_helper(&$record)
	{
		$this->record =& $record;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Validates the current object.
	 * 
	 * This method acts as a wrapper for load_postdata().
	 * 
	 * @param $columns Data sent to load_postdata()
	 * @return boolean
	 */
	function validate($columns = array())
	{
		if(count($columns))
		{
			$this->record->load_postdata($columns);
		}
		
		return $this->record->__instance->validation->validate($this->record);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the validation errors.
	 * 
	 * @return array
	 */
	function errors()
	{
		return $this->errors;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Generate a populated form from the current object.
	 */
	function form($settings = array())
	{
		$settings['data'] =& $this->record;
		return $this->record->__instance->validation->form($settings);
	}
}


/* End of file validation.php */
/* Location: ./application/libraries/ignitedrecord/behaviours/validation.php */
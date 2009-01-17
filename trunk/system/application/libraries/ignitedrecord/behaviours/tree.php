<?php
/*
 * Created on 2008 Apr 12
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
 * The tree behaviour for the IgnitedRecord class.
 * 
 * Provides the IgnitedRecord class with methods for handling trees, and adds a child helper.
 * 
 * @author Martin Wernstahl <m4rw3r@gmail.com>
 * @par Copyright
 * Copyright (c) 2008, Martin Wernstahl <m4rw3r@gmail.com>
 * 
 * @todo Add more methods from MPTtree
 */
class IgnitedRecord_tree{
	/**
	 * Cache for the root node.
	 */
	var $root;
	
	// --------------------------------------------------------------------
	
	function IgnitedRecord_tree(&$ORM, $opts){
		// set opts
		$opts['table'] = $ORM->table;
		$opts['left']  = isset($opts['left'])  ? $opts['left']  : 'lft';
		$opts['right'] = isset($opts['right']) ? $opts['right'] : 'rgt';
		$opts['id']    = $ORM->id_col;
		$opts['title'] = isset($opts['title']) ? $opts['title'] : 'title';
		
		// save reference to IgnitedRecord class
		$this->ORM =& $ORM;
		$this->opts = $opts;
		
		// load mpttree
		if(!class_exists('MPTtree'))
			require_once APPPATH.'/models/mpttree.php';
		
		$this->mpttree = new MPTtree();
		$this->mpttree->set_opts($opts);
		
		// add child helper
		$ORM->child_class_helpers['tree'] = 'IgnitedRecord_tree_helper';
		
		// hooks
		$ORM->add_hook('post_delete',array(&$this,'_post_delete'));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the node at the end of the path $path.
	 * 
	 * @param $path The path to the requested node, this/root node not included
	 * @param $separator The separator in the path, not needed if input is array, default: '/'
	 * 
	 * @return An IgnitedRecord_record if node was found, false otherwise
	 */
	function &xpath($path,$separator = '/')
	{
		$data = $this->mpttree->xpath($path,$separator);
		
		if($data == false)
			return $data;
		
		$obj =& $this->ORM->_dbobj2ORM($data);
		
		return $obj;
	}	

	/**
	 * Runs remove gaps on the tree after delete, promotes children.
	 */
	function _post_delete()
	{
		// remove_gaps() doesn't autoaquire a lock, so acquire a lock
		$this->mpttree->lock_tree_table();
		$this->mpttree->remove_gaps();
		$this->mpttree->unlock_tree_table();
	}
}
/**
 * Child class helper for the IgnitedRecord_tree behaviour.
 */
class IgnitedRecord_tree_helper{
	/**
	 * The IgnitedRecord instance.
	 */
	var $orm;
	
	/**
	 * The record associated with this helper.
	 */
	var $record;
	
	/**
	 * The options supplied to IgnitedRecord_tree (and MPTtree).
	 */
	var $opts;
	
	/**
	 * The MPTtree instance.
	 */
	var $mpttree;
	
	/**
	 * Cache for children for this node.
	 */
	var $children;
	
	/**
	 * Cache for parent of this node.
	 */
	var $parent;
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	function IgnitedRecord_tree_helper(&$record)
	{
		$this->record =& $record;
		$this->orm =& $record->__instance;
		$this->opts =& $record->__instance->tree->opts;
		$this->mpttree =& $this->orm->tree->mpttree;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns true if this node is the root node of a tree.
	 * 
	 * @return bool
	 */
	function is_root()
	{
		return ($this->record->{$this->opts['left']} == 1);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Checks if this node is part of a tree.
	 * 
	 * The criteria for a node which is part of a tree is:
	 * The node must be saved in the tree (the record's __id property is set)
	 * The left and right column values must be set
	 */
	function is_orphan()
	{
		return (!$this->record->in_db() ||
				! (isset($this->record->{$this->opts['left']}) &&
					$this->record->{$this->opts['left']} != null &&
					isset($this->record->{$this->opts['right']}) &&
					$this->record->{$this->opts['right']} != null));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the root node.
	 * 
	 * @return an IgnitedRecord_object, or false if no root exists
	 */
	function &root()
	{
		if(isset($this->orm->tree->root) && $this->orm->tree->root != false)
			return $root;
		
		$this->orm->tree->root = $this->orm->find_by($this->opts['left'],1);
		
		return $this->orm->tree->root;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the parent of the current node.
	 * 
	 * @return An IgnitedRecord_record, or false if no parent is found
	 */
	function &parent()
	{
		if(isset($this->parent))
			return $this->parent;
		
		if($this->is_orphan() OR $this->is_root())
		{
			$false = false;
			return $false;
		}
		
		$this->orm->order_by($this->lft_col, 'desc');
		$this->parent =&  $this->orm->find_by(array($this->opts['left'].' <',$this->opts['right'].' >'),array($this->record->{$this->opts['left']},$this->record->{$this->opts['right']}));
		
		return $this->parent;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns all children of this node.
	 * 
	 * @return An array containing IgnitedRecord_records
	 */
	function &children()
	{
		if(isset($this->children))
			return $this->children;
		
		$ret = array();
		
		if($this->is_orphan())
			return $ret;
		
		foreach($this->mpttree->get_children($this->record->{$this->opts['left']},$this->record->{$this->opts['right']}) as $child)
		{
			$ref =& $this->orm->_dbobj2ORM($child);
			$ref->tree->parent =& $this;
			$ret[] =& $ref;
		}
		$this->children =& $ret;
		
		return $this->children;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns true if this node has children.
	 * 
	 * @return bool
	 */
	function has_children()
	{
		return ($this->is_orphan() && ($this->record->{$this->opts['right']} - $this->record->{$this->opts['left']} > 1));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the number of childrens that this node has.
	 * 
	 * @return int
	 */
	function count_children()
	{
		// If this is an orphan, return 0
		if($this->is_orphan())
			return 0;
		
		// if we already have the children loaded, count that array
		if(isset($this->children))
			return count($this->children);
		
		// call MPTtree
		return $this->mpttree->count_children($this->record->{$this->opts['left']},$this->record->{$this->opts['right']});
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns true if this node is a child of $node.
	 * 
	 * @since 0.1.1
	 * 
	 * @param $node The node
	 * 
	 * @return true if this node is a child of $node
	 */
	function is_child_of(&$node)
	{
		foreach($node->children() as $child)
		{
			if($this->record->__id == $child->__id)
				return true;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns all descendants of this node.
	 * 
	 * @param $level_col If to add an additional column containing the node level, default: false
	 * 
	 * @return An array containing all descendants
	 */
	function &descendants($level_col = false)
	{
		$ret = array();
		
		if($this->is_orphan())
			return array();
		
		foreach($this->mpttree->get_descendants($this->record->{$this->opts['left']},$this->record->{$this->opts['right']},$level_col) as $child)
		{
			$ref =& $this->orm->_dbobj2ORM($child);
			//$ref->tree->parent =& $this;
			$ret[] =& $ref;
		}
		
		return $ret;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the number of descendants this node has.
	 * 
	 * If this node is an orphan, 0 is returned
	 */
	function count_descendants()
	{
		// If this is an orphan, return 0
		if($this->is_orphan())
			return 0;
		
		return $this->mpttree->count_descendants($this->record->{$this->opts['left']},$this->record->{$this->opts['right']});
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns true if this node is a descendant of $node.
	 * 
	 * @since 0.1.1
	 * 
	 * @param $node The node
	 * 
	 * @return true if this node is a descendant of $node.
	 */
	function is_descendant_of(&$node){
		return $node->tree->mpttree->tree_table == $this->mpttree->tree_table &&
			!$node->tree->is_orphan() &&
			!$this->is_orphan() &&
			$node->{$this->opts['left']} > $this->record->{$this->opts['left']} &&
			$node->{$this->opts['right']} < $this->record->{$this->opts['left']};
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the path to the current node.
	 * 
	 * The segments consists of the title column specified in the MPTtree instance used by this IgnitedRecord_record node.
	 * If $true_path is set to false, the path can be used by xpath() directly.
	 * 
	 * @param $true_path Whether to include the root node in the path, default: false
	 * 
	 * @return An array with the path to the current node (including or excluding root node, depending on $true_path), false if node is an orphan
	 * 
	 * @todo Replace with an sql query that fetches all instead (add a method to MPTtree that returns the node's path)
	 */
	function path($true_path = false){
		// if this is an orphan, return false
		if($this->is_orphan())
				return false;
		
		$path = array($this->record->{$this->opts['title']});
		$parent =& $this->parent();
		
		while($parent != false)
		{
			$path[] = $parent->record->{$this->opts['title']};
			$parent =& $parent->tree->parent();
		}
		
		if(!$true_path)
		{
			array_pop($path);
		}
		
		return array_reverse($path);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 *  Returns the node at the end of the path $path.
	 * 
	 * If this node is an orphan, relative paths will not work (defaults to full paths instead)
	 * 
	 * @param $path The path to the requested node, this/root node not included
	 * @param $separator The separator in the path, not needed if input is array, default: '/'
	 * @param $relative If the path should be relative, default: true
	 * 
	 * @return An IgnitedRecord_record if node was found, false otherwise
	 */
	function &xpath($path, $separator = '/', $relative = true)
	{
		if($relative && !$this->is_orphan())
			$data = $this->mpttree->xpath($path,$separator,$this->record->{$this->opts['left']});
		else
			$data = $this->mpttree->xpath($path,$separator);
		
		if($data == false)
			return $data;
		
		$obj =& $this->orm->_dbobj2ORM($data);
		
		return $obj;
	}
	
	/////////////////////////////////////
	// Insert methods
	/////////////////////////////////////
	
	/**
	 * Inserts this node as a root node, if the tree has none and this node is not part of any tree.
	 * 
	 * @return True if inserted, false if fail (if root exists or if this node is already part of a tree)
	 */
	function insert_as_root()
	{
		if(!$this->is_orphan() || $this->root())
			return false;
		
		$this->mpttree->insert_root($this->orm->_strip_data($this->record));
		
		// grab id
		$Ci =& get_instance();
		$this->record->__id = $Ci->db->insert_id();
		
		// set defaults
		$this->record->{$this->opts['left']} = 1;
		$this->record->{$this->opts['right']} = 2;
		
		// save in cache
		$this->root[$this->mpttree->tree_table] =& $this;
		
		return true;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Inserts this node as a sibling above the node $node.
	 * 
	 * Will not insert node if the node is already part of a tree.
	 * 
	 * @param $node The sibling
	 * @return True if success, false otherwise
	 * @todo Update the parent node, so it's children gets updated
	 */
	function insert_above(&$node)
	{
		if($node->tree->is_root() || !$this->is_orphan() || $node->tree->is_orphan() || $node->tree->mpttree->tree_table != $this->mpttree->tree_table)
			return false;
		
		if($ret = $this->instance->insert_node_before($node->{$this->opts['left']},$this->orm->_strip_data($this->record)))
		{
			list($this->record->{$this->opts['left']},$this->record->{$this->opts['right']},$this->record->__id) = $ret;
			$this->parent =& $node->tree->parent();
			
			return true;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Inserts this node as a sibling below the node $node.
	 * 
	 * Will not insert node if the node is already part of a tree.
	 * 
	 * @param $node The sibling
	 * @return True if successful, false otherwise
	 * 
	 * @todo Update the parent node, so it's children gets updated
	 */
	function insert_below(&$node)
	{
		if($node->tree->is_root() || !$this->is_orphan()  || $node->tree->is_orphan() || $node->tree->mpttree->tree_table != $this->mpttree->tree_table)
			return false;
		
		if($ret = $this->instance->insert_node_after($node->{$this->opts['left']},$this->orm->_strip_data($this->record)))
		{
			list($this->record->{$this->opts['left']},$this->record->{$this->opts['right']},$this->record->__id) = $ret;
			$this->parent =& $node->tree->parent();
			
			return true;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Inserts this node as the first child of the node $node.
	 * 
	 * Will not insert node if the node is already part of a tree.
	 * 
	 * @param $node The node to be parent
	 * 
	 * @return True if success, false otherwise
	 */
	function insert_as_first_child_of(&$node){
		if(!$this->is_orphan() || 
			$node->tree->is_orphan() || 
			$node->tree->mpttree->tree_table != $this->mpttree->tree_table)
				return false;
		
		if($ret = $this->instance->append_node($node->{$this->opts['left']},$this->orm->_strip_data($this->record)))
		{
			list($this->record->{$this->opts['left']},$this->record->{$this->opts['right']},$this->record->__id) = $ret;
			$this->parent =& $node;
			
			if($this->parent->tree->children != null) // check if parent already has data
				$this->array_unshift_ref($this->parent->tree->children,$this);
			
			return true;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Inserts this node as the last child of the node $node.
	 * 
	 * Will not insert node if the node is already part of a tree.
	 * 
	 * @param $node The node to be parent
	 * 
	 * @return True if success, false otherwise
	 */
	function insert_as_last_child_of(&$node)
	{
		if(!$this->is_orphan() || $node->tree->is_orphan() || $node->tree->mpttree->tree_table != $this->mpttree->tree_table)
			return false;
		
		if($ret = $this->instance->append_node_last($node->{$this->opts['left']},$this->orm->_strip_data($this->record)))
		{
			list($this->record->{$this->opts['left']},$this->record->{$this->opts['right']},$this->id) = $ret;
			$this->parent =& $node;
			
			if($this->parent->tree->children != null) // check if parent already has data
				$this->parent->tree->children[] =& $this;
			
			return true;
		}
		return false;
	}
	
	/////////////////////////////////////
	// Move methods
	/////////////////////////////////////
	
	/**
	 * Moves this node to the position of sibling above the node $node.
	 * 
	 * @param $node The sibling node
	 * 
	 * @return false if insert failed, true if success
	 * 
	 * @todo Update the parent node, so it's children gets updated
	 */
	function move_above(&$node)
	{
		if($this->is_orphan())
			return $this->insert_above($node);
		
		if($node->tree->is_orphan() || $node->tree->is_root() || $this->is_root() || $node->tree->mpttree->tree_table != $this->mpttree->tree_table)
			return false;
		
		if($ret = $this->mpttree->move_node_before($this->record->{$this->opts['left']},$node->{$this->opts['left']}))
		{
			list($this->record->{$this->opts['left']} , $this->record->{$this->opts['right']}) = $ret;
			$this->parent =& $node->tree->parent();
			
			return true;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Moves this node to the position of sibling below the node $node.
	 * 
	 * @param $node The sibling node
	 * 
	 * @return false if insert failed, true if success
	 * 
	 * @todo Update the parent node, so it's children gets updated
	 */
	function move_below(&$node)
	{
		if($this->is_orphan())
			return $this->insert_below($node);
		
		if($node->tree->is_root() || $this->is_root() || $node->tree->is_orphan() || $node->tree->mpttree->tree_table != $this->mpttree->tree_table)
			return false;
		
		if($ret = $this->mpttree->move_node_after($this->record->{$this->opts['left']},$node->{$this->opts['left']}))
		{
			list($this->record->{$this->opts['left']} , $this->record->{$this->opts['right']}) = $ret;
			$this->parent =& $node->tree->parent();
			
			return true;
		}
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Moves this node to the position of first child of the node $node.
	 * 
	 * @param $node The node to be parent
	 * 
	 * @return false if insert failed, true if success
	 */	
	function move_to_first_child_of(&$node)
	{
		if($this->is_orphan())
			return $this->insert_as_first_child_of($node);
		
		if($this->is_root() || $node->tree->is_orphan() || $node->tree->mpttree->tree_table != $this->mpttree->tree_table)
			return false;
		
		if($ret = $this->mpttree->move_node_append($this->record->{$this->opts['left']},$node->{$this->opts['left']}))
		{
			list($this->record->{$this->opts['left']} , $this->record->{$this->opts['right']}) = $ret;
			$this->parent =& $node;
			
			if($this->parent->tree->children != null) // check if parent already has data
				$this->array_unshift_ref($this->parent->tree->children,$this);
			
			return true;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Moves this node to the position of last child of the node $node.
	 * 
	 * @param $node The node to be parent
	 * 
	 * @return false if insert failed, true if success
	 */
	function move_to_last_child_of(&$node)
	{
		if($this->record->__id == null)
			return $this->insert_as_last_child_of($node);
		
		if($this->is_root() || $node->tree->is_orphan() || $node->tree->mpttree->tree_table != $this->mpttree->tree_table)
			return false;
		
		if($ret = $this->mpttree->move_node_append_last($this->record->{$this->opts['left']},$node->{$this->opts['left']}))
		{
			list($this->record->{$this->opts['left']} , $this->record->{$this->opts['right']}) = $ret;
			$this->parent =& $node;
			
			if($this->parent->tree->children != null) // check if parent already has data
				$this->parent->tree->children[] =& $this;
			
			return true;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Unshifts an array with a reference
	 * 
	 * Prepend a reference to an element to the beginning of an array.
	 * Renumbers numeric keys, so $value is always inserted to $array[0]
	 * 
	 * @param $array array
	 * @param $value mixed
	 * 
	 * @return an int from array_unshift()
	 */
	function array_unshift_ref(&$array, &$value)
	{
		$return = array_unshift($array,'');
		$array[0] =& $value;
		return $return;
	}
}
/**
 * @}
 */

/* End of file tree.php */
/* Location: ./application/libraries/ignitedrecord/behaviours/tree.php */
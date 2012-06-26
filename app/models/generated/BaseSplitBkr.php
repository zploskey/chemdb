<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('SplitBkr', 'dev_al_be_quartz_chem');

/**
 * BaseSplitBkr
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $bkr_number
 * @property Doctrine_Collection $Split
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseSplitBkr extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('split_bkr');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('bkr_number', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => '',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Split', array(
             'local' => 'id',
             'foreign' => 'split_bkr_id'));
    }
}
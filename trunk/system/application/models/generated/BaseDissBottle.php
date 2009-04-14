<?php

/**
 * BaseDissBottle
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $bottle_number
 * @property Doctrine_Collection $Analysis
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
abstract class BaseDissBottle extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('diss_bottle');
        $this->hasColumn('id', 'integer', 4, array('type' => 'integer', 'unsigned' => '1', 'primary' => true, 'autoincrement' => true, 'length' => '4'));
        $this->hasColumn('bottle_number', 'string', 2147483647, array('type' => 'string', 'length' => '2147483647'));
    }

    public function setUp()
    {
        $this->hasMany('Analysis', array('local' => 'id',
                                         'foreign' => 'diss_bottle_id'));
    }
}
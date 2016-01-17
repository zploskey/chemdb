<?php

/**
 * BaseBeStdSeries
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $code
 * @property string $notes
 * @property Doctrine_Collection $BeAmsStd
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBeStdSeries extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('be_std_series');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => '1',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('code', 'string', 60, array(
             'type' => 'string',
             'fixed' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => '60',
             ));
        $this->hasColumn('notes', 'string', 2147483647, array(
             'type' => 'string',
             'fixed' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => '2147483647',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('BeAmsStd', array(
             'local' => 'id',
             'foreign' => 'be_std_series_id'));
    }
}
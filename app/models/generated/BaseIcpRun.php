<?php

/**
 * BaseIcpRun
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property integer $split_id
 * @property integer $run_num
 * @property float $al_result
 * @property float $be_result
 * @property enum $use_al
 * @property enum $use_be
 * @property Split $Split
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
abstract class BaseIcpRun extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('icp_run');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => '1',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('split_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => '1',
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             'length' => '4',
             ));
        $this->hasColumn('run_num', 'integer', 1, array(
             'type' => 'integer',
             'unsigned' => '1',
             'primary' => false,
             'default' => '1',
             'notnull' => true,
             'autoincrement' => false,
             'length' => '1',
             ));
        $this->hasColumn('al_result', 'float', 2147483647, array(
             'type' => 'float',
             'unsigned' => 0,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             'length' => '2147483647',
             ));
        $this->hasColumn('be_result', 'float', 2147483647, array(
             'type' => 'float',
             'unsigned' => 0,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             'length' => '2147483647',
             ));
        $this->hasColumn('use_al', 'enum', 1, array(
             'type' => 'enum',
             'fixed' => 0,
             'values' =>
             array(
              0 => 'y',
              1 => 'n',
             ),
             'primary' => false,
             'default' => 'y',
             'notnull' => true,
             'autoincrement' => false,
             'length' => '1',
             ));
        $this->hasColumn('use_be', 'enum', 1, array(
             'type' => 'enum',
             'fixed' => 0,
             'values' =>
             array(
              0 => 'y',
              1 => 'n',
             ),
             'primary' => false,
             'default' => 'y',
             'notnull' => true,
             'autoincrement' => false,
             'length' => '1',
             ));
    }

    public function setUp()
    {
        $this->hasOne('Split', array(
             'local' => 'split_id',
             'foreign' => 'id'));
    }
}
<?php

/**
 * BaseBatch
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $al_carrier_id
 * @property integer $be_carrier_id
 * @property string $owner
 * @property string $description
 * @property date $start_date
 * @property date $split_date
 * @property date $icp_date
 * @property string $be_carrier_name
 * @property double $wt_be_carrier_init
 * @property double $wt_be_carrier_final
 * @property string $al_carrier_name
 * @property double $wt_al_carrier_init
 * @property double $wt_al_carrier_final
 * @property string $notes
 * @property enum $completed
 * @property string $spreadsheet_name
 * @property string $csv_name
 * @property AlCarrier $AlCarrier
 * @property BeCarrier $BeCarrier
 * @property Doctrine_Collection $Analysis
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBatch extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('batch');
        $this->hasColumn('id', 'integer', 2, array(
             'type' => 'integer',
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '2',
             ));
        $this->hasColumn('al_carrier_id', 'integer', 1, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '1',
             ));
        $this->hasColumn('be_carrier_id', 'integer', 1, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '1',
             ));
        $this->hasColumn('owner', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('start_date', 'date', 25, array(
             'type' => 'date',
             'default' => '0000-00-00',
             'notnull' => true,
             'length' => '25',
             ));
        $this->hasColumn('split_date', 'date', 25, array(
             'type' => 'date',
             'default' => '0000-00-00',
             'notnull' => true,
             'length' => '25',
             ));
        $this->hasColumn('icp_date', 'date', 25, array(
             'type' => 'date',
             'default' => '0000-00-00',
             'notnull' => true,
             'length' => '25',
             ));
        $this->hasColumn('be_carrier_name', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '',
             ));
        $this->hasColumn('wt_be_carrier_init', 'double', 53, array(
             'type' => 'double',
             'default' => '0',
             'notnull' => true,
             'length' => '53',
             'scale' => '30',
             ));
        $this->hasColumn('wt_be_carrier_final', 'double', 53, array(
             'type' => 'double',
             'default' => '0',
             'notnull' => true,
             'length' => '53',
             'scale' => '30',
             ));
        $this->hasColumn('al_carrier_name', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '',
             ));
        $this->hasColumn('wt_al_carrier_init', 'double', 53, array(
             'type' => 'double',
             'default' => '0',
             'notnull' => true,
             'length' => '53',
             'scale' => '30',
             ));
        $this->hasColumn('wt_al_carrier_final', 'double', 53, array(
             'type' => 'double',
             'default' => '0',
             'notnull' => true,
             'length' => '53',
             'scale' => '30',
             ));
        $this->hasColumn('notes', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('completed', 'enum', 1, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'y',
              1 => 'n',
             ),
             'default' => 'n',
             'notnull' => true,
             'length' => '1',
             ));
        $this->hasColumn('spreadsheet_name', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('csv_name', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('AlCarrier', array(
             'local' => 'al_carrier_id',
             'foreign' => 'id'));

        $this->hasOne('BeCarrier', array(
             'local' => 'be_carrier_id',
             'foreign' => 'id'));

        $this->hasMany('Analysis', array(
             'local' => 'id',
             'foreign' => 'batch_id'));
    }
}
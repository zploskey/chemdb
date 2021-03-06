<?php

/**
 * BaseAlCarrier
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property double $al_conc
 * @property double $del_al_conc
 * @property double $r26to27
 * @property double $r26to27_error
 * @property date $in_service_date
 * @property string $mfg_lot_no
 * @property string $owner
 * @property string $notes
 * @property string $in_use
 * @property Doctrine_Collection $Batch
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAlCarrier extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('al_carrier');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('al_conc', 'double', 53, array(
             'type' => 'double',
             'length' => '53',
             'scale' => '30',
             ));
        $this->hasColumn('del_al_conc', 'double', 53, array(
             'type' => 'double',
             'length' => '53',
             'scale' => '30',
             ));
        $this->hasColumn('r26to27', 'double', 53, array(
             'type' => 'double',
             'length' => '53',
             'scale' => '30',
             ));
        $this->hasColumn('r26to27_error', 'double', 53, array(
             'type' => 'double',
             'length' => '53',
             'scale' => '30',
             ));
        $this->hasColumn('in_service_date', 'date', 25, array(
             'type' => 'date',
             'length' => '25',
             ));
        $this->hasColumn('mfg_lot_no', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('owner', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('notes', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('in_use', 'string', null, array(
             'type' => 'string',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Batch', array(
             'local' => 'id',
             'foreign' => 'al_carrier_id'));
    }
}
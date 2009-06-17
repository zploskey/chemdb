<?php

/**
 * BaseAnalysis
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $batch_id
 * @property integer $number_within_batch
 * @property string $sample_name
 * @property string $sample_type
 * @property string $diss_bottle_number
 * @property float $wt_diss_bottle_tare
 * @property float $wt_diss_bottle_sample
 * @property float $wt_be_carrier
 * @property float $wt_al_carrier
 * @property float $wt_diss_bottle_total
 * @property string $notes
 * @property integer $diss_bottle_id
 * @property Batch $Batch
 * @property DissBottle $DissBottle
 * @property Doctrine_Collection $AlcheckAnalysis
 * @property Doctrine_Collection $Split
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
abstract class BaseAnalysis extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('analysis');
        $this->hasColumn('id', 'integer', 2, array('type' => 'integer', 'unsigned' => '1', 'primary' => true, 'autoincrement' => true, 'length' => '2'));
        $this->hasColumn('batch_id', 'integer', 2, array('type' => 'integer', 'default' => '0', 'notnull' => true, 'length' => '2'));
        $this->hasColumn('number_within_batch', 'integer', 1, array('type' => 'integer', 'default' => '0', 'notnull' => true, 'length' => '1'));
        $this->hasColumn('sample_name', 'string', 2147483647, array('type' => 'string', 'default' => '', 'notnull' => true, 'length' => '2147483647'));
        $this->hasColumn('sample_id', 'integer', 2, array('type' => 'integer', 'default' => '0', 'notnull' => true, 'length' => '2'));
        $this->hasColumn('sample_type', 'string', 2147483647, array('type' => 'string', 'default' => '', 'notnull' => true, 'length' => '2147483647'));
        $this->hasColumn('diss_bottle_number', 'string', 2147483647, array('type' => 'string', 'default' => '', 'notnull' => true, 'length' => '2147483647'));
        $this->hasColumn('wt_diss_bottle_tare', 'float', 2147483647, array('type' => 'float', 'default' => '0', 'notnull' => true, 'length' => '2147483647'));
        $this->hasColumn('wt_diss_bottle_sample', 'float', 2147483647, array('type' => 'float', 'default' => '0', 'notnull' => true, 'length' => '2147483647'));
        $this->hasColumn('wt_be_carrier', 'float', 2147483647, array('type' => 'float', 'default' => '0', 'notnull' => true, 'length' => '2147483647'));
        $this->hasColumn('wt_al_carrier', 'float', 2147483647, array('type' => 'float', 'default' => '0', 'notnull' => true, 'length' => '2147483647'));
        $this->hasColumn('wt_diss_bottle_total', 'float', 2147483647, array('type' => 'float', 'default' => '0', 'notnull' => true, 'length' => '2147483647'));
        $this->hasColumn('notes', 'string', 2147483647, array('type' => 'string', 'default' => '', 'notnull' => true, 'length' => '2147483647'));
        $this->hasColumn('diss_bottle_id', 'integer', 4, array('type' => 'integer', 'length' => '4'));
    }

    public function setUp()
    {
        $this->hasOne('Batch', array('local' => 'batch_id',
                                     'foreign' => 'id'));

        $this->hasOne('Sample', array('local' => 'sample_id',
                                      'foreign' => 'id'));

        $this->hasOne('DissBottle', array('local' => 'diss_bottle_id',
                                          'foreign' => 'id'));

        $this->hasMany('AlcheckAnalysis', array('local' => 'id',
                                                'foreign' => 'analysis_id'));

        $this->hasMany('Split', array('local' => 'id',
                                      'foreign' => 'analysis_id'));
    }
}
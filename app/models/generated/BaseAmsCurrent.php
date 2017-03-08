<?php

/**
 * BaseAmsCurrent
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property double $current
 * @property integer $be_ams_id
 * @property integer $al_ams_id
 * @property BeAms $BeAms
 * @property AlAms $AlAms
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAmsCurrent extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('ams_current');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('current', 'double', 53, array(
             'type' => 'double',
             'length' => '53',
             'scale' => '30',
             ));
        $this->hasColumn('be_ams_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '4',
             ));
        $this->hasColumn('al_ams_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '4',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('BeAms', array(
             'local' => 'be_ams_id',
             'foreign' => 'id'));

        $this->hasOne('AlAms', array(
             'local' => 'al_ams_id',
             'foreign' => 'id'));
    }
}
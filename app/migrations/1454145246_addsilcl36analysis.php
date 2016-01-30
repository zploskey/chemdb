<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Addsilcl36analysis extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('sil_cl36_analysis', array(
             'id' => 
             array(
              'type' => 'integer',
              'length' => 4,
              'fixed' => false,
              'unsigned' => true,
              'primary' => true,
              'autoincrement' => true,
             ),
             'sample_id' => 
             array(
              'type' => 'integer',
              'length' => 4,
              'fixed' => false,
              'unsigned' => true,
              'primary' => false,
              'notnull' => false,
              'autoincrement' => false,
             ),
             'sample_name' => 
             array(
              'type' => 'string',
              'length' => 255,
              'fixed' => false,
              'unsigned' => false,
              'primary' => false,
              'default' => '',
              'notnull' => true,
              'autoincrement' => false,
             ),
             'cl36_batch_id' => 
             array(
              'type' => 'integer',
              'length' => 4,
              'fixed' => false,
              'unsigned' => true,
              'primary' => false,
              'notnull' => false,
              'autoincrement' => false,
             ),
             'calib_id' => 
             array(
              'type' => 'integer',
              'length' => 4,
              'fixed' => false,
              'unsigned' => true,
              'primary' => false,
              'notnull' => false,
              'autoincrement' => false,
             ),
             'cl37_spike_id' => 
             array(
              'type' => 'integer',
              'length' => 4,
              'fixed' => false,
              'unsigned' => true,
              'primary' => false,
              'notnull' => false,
              'autoincrement' => false,
             ),
             'sample_type' => 
             array(
              'type' => 'string',
              'length' => 6,
              'fixed' => false,
              'unsigned' => false,
              'primary' => false,
              'default' => 'SAMPLE',
              'notnull' => true,
              'autoincrement' => false,
             ),
             'wt_spike' => 
             array(
              'type' => 'float',
              'length' => 18,
              'fixed' => false,
              'unsigned' => false,
              'primary' => false,
              'notnull' => false,
              'autoincrement' => false,
             ),
             'wt_bkr_tare' => 
             array(
              'type' => 'float',
              'length' => 18,
              'fixed' => false,
              'unsigned' => false,
              'primary' => false,
              'notnull' => false,
              'autoincrement' => false,
             ),
             'wt_bkr_sample' => 
             array(
              'type' => 'float',
              'length' => 18,
              'fixed' => false,
              'unsigned' => false,
              'primary' => false,
              'notnull' => false,
              'autoincrement' => false,
             ),
             ), array(
             'indexes' => 
             array(
             ),
             'primary' => 
             array(
              0 => 'id',
             ),
             ));
    }

    public function down()
    {
        $this->dropTable('sil_cl36_analysis');
    }
}
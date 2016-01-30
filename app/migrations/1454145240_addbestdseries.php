<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Addbestdseries extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('be_std_series', array(
             'id' => 
             array(
              'type' => 'integer',
              'length' => 4,
              'fixed' => false,
              'unsigned' => true,
              'primary' => true,
              'autoincrement' => true,
             ),
             'code' => 
             array(
              'type' => 'string',
              'length' => 60,
              'fixed' => false,
              'unsigned' => false,
              'primary' => false,
              'notnull' => false,
              'autoincrement' => false,
             ),
             'notes' => 
             array(
              'type' => 'string',
              'fixed' => false,
              'unsigned' => false,
              'primary' => false,
              'notnull' => false,
              'autoincrement' => false,
              'length' => NULL,
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
        $this->dropTable('be_std_series');
    }
}
<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Addsplitbkr extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('split_bkr', array(
             'id' => 
             array(
              'type' => 'integer',
              'length' => 4,
              'fixed' => false,
              'unsigned' => true,
              'primary' => true,
              'autoincrement' => true,
             ),
             'bkr_number' => 
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
        $this->dropTable('split_bkr');
    }
}
<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Batch extends BaseBatch
{
	public function setUp()
	{
		parent::setUp();
		
		$this->hasMany('Analysis as Analyses', array(
				'local' => 'id',
				'foreign' => 'batch_id'
			)
		);
	}
}
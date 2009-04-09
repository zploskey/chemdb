<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Split extends BaseSplit
{
	public function setUp() {
		parent::setUp();

		$this->hasMany('IcpRun as IcpRuns', array(
				'local' => 'id',
				'foreign' => 'split_id'
			)
		);

		$this->hasOne('Analysis', array(
				'local' => 'analysis_id',
				'foreign' => 'id'
			)
		);
	}

}
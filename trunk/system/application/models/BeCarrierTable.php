<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class BeCarrierTable extends Doctrine_Table
{
	public function getList()
	{
		$q = Doctrine_Query::create()
			->from('BeCarrier bec')
			->select('bec.id, bec.name')
			->execute();
		return $q;
	}
}
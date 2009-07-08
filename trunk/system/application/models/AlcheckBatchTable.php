<?php 

class AlcheckBatchTable extends Doctrine_Table
{
    /**
     *
     * @return Doctrine_Collection
     */
    public function findRecentBatches($nBatches)
    {
        return Doctrine_Query::create()
            ->from('AlcheckBatch b')
            ->orderBy('b.prep_date desc')
            ->where('b.description != ?', 'Dummy batch')
            ->limit($nBatches)
            ->execute();
    }
    
    /**
     *
     * @return Doctrine_Collection
     */
    public function findAllBatches()
    {
        return Doctrine_Query::create()
            ->from('AlcheckBatch b')
            ->orderBy('b.prep_date desc')
            ->where('b.description != ?', 'Dummy batch')
            ->execute();
    }
}
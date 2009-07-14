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
    
    /**
     * Used in Alchecks repeatedly to grab analyses and sample names, sorted ascending.
     * @return Doctrine_Query that retrieves an AlcheckBatch, analyses and samples (just names).
     */
    public function getJoinQuery($batch_id)
    {
        return Doctrine_Query::create()
            ->from('AlcheckBatch b')
            ->leftJoin('b.AlcheckAnalysis a')
            ->leftJoin('a.Sample s')
            ->select('b.*, a.*, s.id, s.name')
            ->where('b.id = ?', $batch_id)
            ->orderBy('a.number_within_batch ASC'); 
    }
}
<?php
/**
 * Interfaces with the AlcheckBatch table in the Database.
 */
class AlcheckBatchTable extends Doctrine_Table
{
    /**
     * Returns a Doctrine_Collection of AlcheckBatch with the newest batches
     * listed first. It excludes all "dummy batches" produces by quick_add.
     * @return Doctrine_Collection
     */
    public function findAllBatches()
    {
        return Doctrine_Query::create()
            ->from('AlcheckBatch b')
            ->orderBy('b.id desc')
            ->where('b.description != ?', 'Dummy batch')
            ->execute();
    }

    /**
     * Generates a query to grab analyses and sample names, sorted ascending.
     * @return Doctrine_Query
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

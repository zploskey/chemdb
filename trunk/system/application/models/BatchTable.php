<?php

class BatchTable extends Doctrine_Table
{
    public function countSplits($batch_id)
    {
        $batch = $this->createQuery('b')
          //  ->select('b.id, a.id, s.id')
            ->where('b.id = ?', $batch_id)
            ->leftJoin('b.Analysis a')
            ->leftJoin('a.Split s')
            ->fetchOne();
        
        $count = 0;
        foreach ($batch->Analysis as $a) {
            $count += $batch->Split->count();
        }
        return $count;
    }
    
    /**
     *
     * @return Doctrine_Collection
     */
    public function findOpenBatches()
    {
        return Doctrine_Query::create()
            ->from('Batch b')
            ->select('b.id, b.owner, b.description, b.start_date')
            ->where('completed = ?', 'n')
            ->orderBy('b.start_date desc')
            ->execute();
    }
    
    /**
     *
     * @return Doctrine_Collection
     */
    public function findAllBatches()
    {
        return Doctrine_Query::create()
            ->from('Batch b')
            ->select('b.id, b.owner, b.description, b.start_date')
            ->orderBy('b.start_date desc')
            ->execute();
    }
    
    /**
     * Get batch collection for Icp Quality Control Page by the batch's id.
     *
     * @return Doctrine_Collection
     **/
    public function findIcpQualityControl($batch_id)
    {
        return Doctrine_Query::create()
            ->from('Batch b')
            ->leftJoin('b.Analysis a')
            ->leftJoin('a.Sample sa')
            ->leftJoin('b.AlCarrier ac')
            ->leftJoin('b.BeCarrier bc')
            ->leftJoin('a.DissBottle db')
            ->leftJoin('a.Split sp')
            ->leftJoin('sp.IcpRun run')
            ->leftJoin('sp.SplitBkr spb')
            ->where('b.id = ?', $batch_id)
            ->limit(1)
            ->fetchOne();
    }
    
    public function findFinalReport($batch_id)
    {
        return $this->findIcpQualityControl($batch_id);
    }
    
    /**
     * Find batch for the intermediate report by batch id.
     *
     * @return void
     **/
    public function findIntermediateReport($batch_id)
    {
        // these function both use the same collection
        return $this->findIcpQualityControl($batch_id);
    }
    
    /**
     *
     * @param int $carrier_id
     * @param date $start_date
     * @return Doctrine_Collection
     */
    public function findPrevBeCarrierWt($batch_id, $carrier_id)
    {
        return $this->createQuery('b')
            ->select('b.wt_be_carrier_final, b.start_date')
            ->where('b.be_carrier_id = ?', $carrier_id)
            ->addWhere('b.id != ?', $batch_id)
            ->orderBy('b.start_date desc')
            ->limit(1)
            ->fetchOne();
    }
    
    /**
     *
     * @param int $carrier_id
     * @param date $start_date
     * @return Doctrine_Collection
     */
    public function findPrevAlCarrierWt($batch_id, $carrier_id)
    {
        return $this->createQuery('b')
            ->select('b.wt_al_carrier_final, b.start_date')
            ->where('b.al_carrier_id = ?', $carrier_id)
            ->addWhere('b.id != ?', $batch_id)
            ->orderBy('b.start_date desc')
            ->limit(1)
            ->fetchOne();
    }

}
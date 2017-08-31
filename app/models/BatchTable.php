<?php
/**
 * Interfaces with the Batch table
 */
class BatchTable extends Doctrine_Table
{
    /**
     * @param int $batch_id id # of the batch we want to count splits for
     * @return int $count the number of splits in this batch
     */
    public function countSplits($batch_id)
    {
        $batch = $this->createQuery('b')
            ->select('b.id, a.id, s.id')
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
     * Fetches a batch object with Al and Be carrier data.
     *
     * @param mixed $id
     * @return Batch
     **/
    public function findWithCarriers($id)
    {
        return Doctrine_Query::create()
            ->from('Batch b')
            ->leftJoin('b.Analysis a')
            ->leftJoin('b.BeCarrier bec')
            ->leftJoin('b.AlCarrier alc')
            ->where('b.id = ?', $id)
            ->orderBy('a.id ASC')
            ->fetchOne();
    }

    /**
     * @return Batch
     */
    public function findAllBatches()
    {
        return Doctrine_Query::create()
            ->from('Batch b')
            ->select('b.id, b.owner, b.description, b.start_date, b.completed')
            ->orderBy('b.id desc')
            ->execute();
    }

    /**
     * Get batch collection for ICP Quality Control Page by the batch's id.
     *
     * @param $batch_id batch's id value
     * @return Batch
     **/
    public function findCompleteById($batch_id)
    {
        return Doctrine_Query::create()
            ->from('Batch b')
            ->leftJoin('b.Analysis a')
            ->leftJoin('a.Sample sa')
            ->leftJoin('b.AlCarrier ac')
            ->leftJoin('b.BeCarrier bc')
            ->leftJoin('a.DissBottle db')
            ->leftJoin('a.Split sp')
            ->leftJoin('sp.IcpRun r')
            ->leftJoin('sp.SplitBkr spb')
            ->where('b.id = ?', $batch_id)
            ->orderBy('a.id ASC')
            ->addOrderBy('sp.split_num')
            ->addOrderBy('r.run_num')
            ->limit(1)
            ->fetchOne();
    }

    /**
     * Fetches a Batch object with the final beryllium carrier weight and start
     * date fields populated. The object will always have a start_date that is
     * older than the start date of the passed to this function.
     * @param int $carrier_id
     * @param string $start_date a valid MySQL date format
     * @return Batch
     */
    public function findPrevBeCarrierWt($carrier_id, $start_date)
    {
        return $this->createQuery('b')
            ->select('b.wt_be_carrier_final, b.start_date')
            ->where('b.be_carrier_id = ?', $carrier_id)
            ->addWhere('b.start_date < ?', $start_date)
            ->orderBy('b.start_date desc')
            ->limit(1)
            ->fetchOne();
    }

    /**
     * Fetches a Batch object with the final aluminum carrier weight and start
     * date fields populated. The object will always have a start_date that is
     * older than the start date of the passed to this function.
     *
     * @param int $carrier_id
     * @param string $start_date a valid MySQL date format
     * @return Batch
     */
    public function findPrevAlCarrierWt($carrier_id, $start_date)
    {
        return $this->createQuery('b')
            ->select('b.wt_al_carrier_final, b.start_date')
            ->where('b.al_carrier_id = ?', $carrier_id)
            ->addWhere('b.start_date < ?', $start_date)
            ->orderBy('b.start_date desc')
            ->limit(1)
            ->fetchOne();
    }

    /**
     * Generates an array containing calculations for the batch with id $id.
     * The optional $stats parameter can be set to true if statistics such as
     * standard deviation are required.
     * @param $id the batch id
     * @param bool $stats whether or not to calculate statistics
     * @return array
     **/
    public function getReportArray($id, $stats = false)
    {
        $batch = $this->findCompleteById($id);
        if (!$batch) {
            throw new Exception('Batch not found.');
        }
        return $batch->getReportArray($stats);
    }

    /**
     * Locks the batch whose id is $id (sets completed to 'y').
     * @param int $id id of the batch to lock
     */
    public function lock($id)
    {
        return Doctrine_Query::create()
            ->update('Batch')
            ->set('completed', '?', 'y')
            ->where('id = ?', $id)
            ->execute();
    }
}

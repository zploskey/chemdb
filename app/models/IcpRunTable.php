<?php
/**
 * Fetches data from the IcpRun table.
 */
class IcpRunTable extends Doctrine_Table
{
    /**
     * Deletes runs associated with a split if there are more associated splits
     * than $nRemainingRuns.
     * @param Split &$split reference to a split object
     * @param int $nRemaingRuns
     * @param mixed $nRemainingRuns
     */
    public function removeExcessRuns(&$split, $nRemainingRuns)
    {
        return Doctrine_Query::create()
            ->delete('IcpRun')
            ->where('split_id = ?', $split->id)
            ->addWhere('run_num > ?', $nRemainingRuns)
            ->execute();
    }
}

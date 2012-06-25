<?
/**
 * undocumented class
 *
 **/
class AlcheckAnalysisTable extends Doctrine_Table
{
    public function getMostRecentByName($sample_name)
    {
        return $this->createQuery('a')
            ->select('a.*')
            ->where('a.name = ?', $sample_name)
            ->leftJoin('a.Batch b')
            ->orderBy('b.start_date DESC')
            ->fetchOne();
    }
}
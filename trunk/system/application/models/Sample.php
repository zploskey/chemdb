<?php

/**
 * Represents a row in the sample table in the database.
 */
class Sample extends BaseSample
{
    /**
    * Get an array with entries for each analysis and each analysis can have multiple
    * AMS results array(array(ams_result_text, ams_result_text, ...), array(...), ...).
    * @return array(string)
    */
    public function getCalcInputs()
    {
        $ero_text = $exp_text = array(array());
        $i = 0;
        foreach ($this->Analysis as $an) {
            foreach ($an->BeAms as $ams) {
                $exp_text[$i][] = $ams->getAgeCalcInput($ams);
                $ero_text[$i][] = $ams->getErosCalcInput($ams);
            }
            ++$i;
        }
        return array($exp_text, $ero_text);
    }
    
    /**
     * Sets up our join table with Projects.
     * @param void
     */
    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Project', array(
                'refClass' => 'ProjectSample',
                'local' => 'sample_id',
                'foreign' => 'project_id',
            )
        );
    }
}
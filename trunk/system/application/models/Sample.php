<?php

/**
 * Represents a row in the sample table in the database.
 */
class Sample extends BaseSample
{

    /**
    * Calculate Be10 and 26Al concentrations and error, compile them with sample
    * information, and return the input string for the CRONUS calculator for
    * this sample and its AMS measurement. The passed parameters and this sample
    * should be fully populated with data before calling this function.
    * 
    * These calculations are based on:
    *
    * Converting Al and Be isotope ratio measurements to nuclide concentrations in quartz.
    * Greg Balco, May 8, 2006
    * http://hess.ess.washington.edu/math/docs/common/ams_data_reduction.pdf
    * @param $BeAMS BeAms object
    * @param $AlAMS AlAms object
    * @return array of strings array($ageCalcInput, $eroCalcInput)
    */
    public function getCalcInput($BeAMS, $AlAMS=NULL)
    {
        if (isset($BeAMS)) {
            $an = $BeAMS->Analysis;
        } elseif (isset($AlAMS)) {
            $an = $AlAMS->Analysis;
        } else {
            throw new IllegalArgumentException(
                'At least one argument must be an AMS measurement.');
        }
        $batch = $an->Batch;
        $bec = $an->Batch->BeCarrier;
        $alc = $an->Batch->AlCarrier;

        $pressure_flag = ($this->antarctic) ? 'ant' : 'std';

        // we default to a zero aluminum analysis
        if (isset($AlAMS)) {
            // calculate aluminum concentration and error
            // temporary settings until we work out this calculation
            list($al26_conc, $al26_err) = $AlAMS->getConcAl26();
            $al_calc_code = $AlAMS->AlAmsStd->AlStdSeries->code;
        } else {
            $al26_conc = $al26_err = 0;
            $al_calc_code = 'KNSTD';
        }

        // likewise for beryllium
        if (isset($BeAMS)) {
            list($be10_conc, $be10_err) = $BeAMS->getConcBe10();
            $be_calc_code = $BeAMS->BeAmsStd->BeStdSeries->code;
        } else {
            $be10_conc = $be10_err = 0;
            $be_calc_code = 'KNSTD07';
        }

        $entries = array(
            'name' => substr($this->name, 0, 24),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'altitude' => $this->altitude,
            'pressure_flag' => $pressure_flag,
            'thickness' => $this->getThickness(),
            'density' => $this->density,
            'shield_factor' => $this->shield_factor,
            'erosion_rate' => $this->erosion_rate,
            'be10_conc' => $be10_conc,
            'be10_err' => $be10_err,
            'be_calc_code' => $be_calc_code,
            'al26_conc' => $al26_conc,
            'al26_err' => $al26_err,
            'al_calc_code' => $al_calc_code,
        );

        // generate age input
        $ageInput = implode(' ', $entries);
        // generate erosion rate input
        unset($entries['erosion_rate']); // remove erosion rate estimate
        $eroInput = implode(' ', $entries);
        return array($ageInput, $eroInput);
    }

    /**
    * Get an array with entries for each analysis and each analysis can have multiple
    * AMS results array(array(ams_result_text, ams_result_text, ...), array(...), ...).
    * @return array(string)
    */
    public function getCalcInputs()
    {
        $ero_text = $exp_text = array(array());
        $all_exp = $all_ero = '';
        $a = 0; // analysis counter
        foreach ($this->Analysis as $an) {
            $i = 0; // ams result counter
            $all_exp = $all_ero = '';
            foreach ($an->BeAms as $BeAMS) {
                if (isset($an['AlAms'])) {
                    $AlAMS = $an['AlAms'][$i];
                } else {
                    $AlAMS = null;
                }
                list($expLine, $eroLine) = $this->getCalcInput($BeAMS, $AlAMS);
                if ($expLine != '' && $eroLine != '') {
                    $exp_text[$a][] = $expLine;
                    $all_exp .= $expLine . '
                    ';
                    $ero_text[$a][] = $eroLine;
                    $all_ero .= $eroLine . '
                    ';
                }
                ++$i;
            }

            if ($i < $an->AlAms->count()) {
                for ($j = $i; $j < $an->AlAms->count(); $j++)
                list($expLine, $eroLine) = $this->getCalcInput(null, $an->AlAms[$j]);
                if ($expLine != '' && $eroLine != '') {
                    $exp_text[$a][] = $expLine;
                    $all_exp .= $expLine . '
                    ';
                    $ero_text[$a][] = $eroLine;
                    $all_ero .= $eroLine . '
                    ';
                }
            }
            ++$a;
        }

        return array('exp' => $exp_text,
                     'ero' => $ero_text,
                     'all_exp' => trim($all_exp),
                     'all_ero' => trim($all_ero));
    }
    
    /**
     * @return thickness of the sample in cm
     */
    public function getThickness()
    {
        return abs($this->depth_bottom - $this->depth_top);
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
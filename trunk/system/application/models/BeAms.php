<?php

/**
 * BeAms
 * 
 */
class BeAms extends BaseBeAms
{
    /**
     * Holds the input text for the calculator for an exposure age calculation.
     *
     * @var string
     * @access private
     **/
    var $ageCalcInput;

    /**
     * Holds the input text for the calculator for an erosion rate calculation.
     *
     * @var string
     * @access private
     **/
    var $erosCalcInput;

    /**
    * Calculate Be10 and 26Al concentrations and error, compile them with sample
    * information, and return the input string for the CRONUS calculator for
    * this sample and its AMS measurement. 
    * 
    * These calculations are based on:
    *
    * Converting Al and Be isotope ratio measurements to nuclide concentrations in quartz.
    * Greg Balco, May 8, 2006
    * http://hess.ess.washington.edu/math/docs/common/ams_data_reduction.pdf
    * @return string $ageCalcInput
    */
    public function getAgeCalcInput($BeAMS)
    {
        if (isset($this->ageCalcInput) && !$this->isModified(true)) {
            return $this->ageCalcInput;
        }

        if (!isset($this->BeAmsStd) 
            || !isset($this->BeAmsStd->BeStdSeries)) {
            return null;
        }

        $an = $this->Analysis;
        $sample = $an->Sample;
        $batch = $an->Batch;
        $bec = $an->Batch->BeCarrier;
        $alc = $an->Batch->AlCarrier;
        
        if ($sample->antarctic) {
            $pressure_flag = 'ant';
        } else {
            $pressure_flag = 'std';
        }
        
        list($be10_conc, $be10_err) = $an->getConcBe10($BeAMS);

        // we default to a zero aluminum analysis
        if (!isset($an->AlAms)) {
            $al26_conc = $al26_err = 0;
            $al_calc_code = 'KNSTD';
        } else {
            // calculate aluminum concentration and error
            // temporary settings until we work out this calculation
            list($al26_conc, $al26_err) = $an->getConcAl26($an->AlAms[0]);
            $al_calc_code = $an->AlAms[0]->AlAmsStd->AlStdSeries->code;
        }

        $entries = array(
            'name' => substr($sample->name, 0, 24),
            'latitude' => $sample->latitude,
            'longitude' => $sample->longitude,
            'altitude' => $sample->altitude,
            'pressure_flag' => $pressure_flag,
            'thickness' => abs($sample->depth_bottom - $sample->depth_top),
            'density' => $sample->density,
            'shield_factor' => $sample->shield_factor,
            'erosion_rate' => $sample->erosion_rate,
            'be10_conc' => $be10_conc,
            'be10_err' => $be10_err,
            'be_calc_code' => $this->BeAmsStd->BeStdSeries->code,
            'al26_conc' => $al26_conc,
            'al26_err' => $al26_err,
            'al_calc_code' => $al_calc_code,
        );

        // generate age input
        $text = '';
        foreach ($entries as $ent) {
            $text .= $ent . ' ';
        }
        $this->ageCalcInput = $text;
        // generate erosion rate input
        unset($entries['erosion_rate']); // remove erosion rate estimate
        $text = '';
        foreach ($entries as $ent) {
            $text .= $ent . ' ';
        }
        $this->erosCalcInput = $text;
        return $this->ageCalcInput;
    }
    
    /**
     * Gets the input to the CRONUS calculator for calculating an erosion rate.
     *
     * @return string $erosCalcInput
     **/
    public function getErosCalcInput($BeAMS)
    {
        if (isset($this->erosCalcInput) && !$this->isModified(true)) {
            return $this->erosCalcInput;
        }

        $this->getAgeCalcInput($BeAMS); // will set erosCalcInput as a side-effect
        return $this->erosCalcInput;
    }

}
<?php

class Analysis extends BaseAnalysis
{

    public function getMassIcp($element)
    {
        $element = strtolower($element);
        $vals = array();
        foreach ($this->Split as $sp) {
            foreach ($sp->IcpRun as $run) {
                if ($run["use_$element"] == 'y') {
                    // calculate Al mass from ICP measurement, in g
                    $vals[] = 
                        $this->getSolnWt() * $run["$element".'_result'] * $sp->getSolnWt()
                        * 1e-6 / $sp->getSplitWt();
                }
            }
        }

        return meanStdDev($vals);
    }
    
    public function getConcPpm($element)
    {
        return safe_divide(
            array_shift(getMassIcp($element)) * 1e-6,
            $this->getSampleWt());
    }

    public function getSolnWt()
    {
        return $this->wt_diss_bottle_total - $this->wt_diss_bottle_tare;
    }

    public function getSampleWt()
    {
        if ($this->sample_type == "BLANK") {
            return 0;
        }
        return $this->wt_diss_bottle_sample - $this->wt_diss_bottle_tare;
    }

}
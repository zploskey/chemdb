<?php

class Analysis extends BaseAnalysis
{
    public function getMassAlIcp()
    {
        $vals = array();
        foreach ($this->Split as $sp) {
            foreach ($sp->IcpRun as $run) {
                if ($run->use_be == 'y') {
                    // calculate Al mass from ICP measurement, in g
                    $vals[] = 
                        $this->getSolnWt() * $run->al_result * $sp->getSolnWt()
                        * 1e-6 / $sp->getSplitWt();
                }
            }
        }

        return meanStdDev($vals);
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
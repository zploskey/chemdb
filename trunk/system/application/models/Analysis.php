<?php

class Analysis extends BaseAnalysis
{
    public function getConcAl26($AlAMS)
    {
        $alc = $this->Batch->BeCarrier;
        
        // Do calculations on the blank first
        $blank = $this->Batch->getBlank('Al');
        $R_26to27_b = $blank->AlAms[0]->r_to_rstd * $blank->AlAms[0]->AlAmsStd->r26to27;
        list($M_Al_b, $M_Al_b_err) = $blank->getMassAlIcp();
        // note, M_Al_b_err (error for mass of Al in blank) is a standard deviation
        // error propagation for blank
        $n26_b_err_terms = array(
            // error from blank AMS measurement
            $blank->AlAms[0]->exterror * $M_Al_b * AVOGADRO / MM_AL,
            // error from ICP measurement
            $M_Al_b_err * $R_26to27_b * AVOGADRO / MM_AL,
        );
        $n26_b_err = sqrt(sum_of_squares($n26_b_err_terms));

        // Be26/Be27 ratio of the sample
        $R_26to27 = $AlAMS->r_to_rstd * $AlAMS->AlAmsStd->r26to27;
        // mass of the quartz in the sample
        $M_qtz = $this->getSampleWt();
        list($M_Al, $M_Al_err) = $this->getMassAlIcp();
        // Estimate of Be10 concentration of sample
        $al26_conc = ($R_26to27 * $M_Al - $R_26to27_b * $M_Al_b) * AVOGADRO / ($M_qtz * MM_AL);
        // Calculate the error in Al26 concentration:
        // First, define the differentials for each error source. Each is
        // equivalent to del(Number of Be10 atoms)/del(source variable)
        // multiplied by the error in the source variable.
        $err_terms = array(
            $AlAMS->exterror * $M_Al * AVOGADRO / ($M_qtz * MM_AL), // from ams error
            -$n26_b_err / $M_qtz, // error from the atoms of Al26 in blank
            $M_Al_err * $R_26to27 * AVOGADRO / ($M_qtz * MM_AL), // from carrier error
        );
        $al26_err = sqrt(sum_of_squares($err_terms));

        return array($al26_conc, $al26_err);
    }
    
    public function getConcBe10($BeAMS)
    {
        $bec = $this->Batch->BeCarrier;
        $blank = $this->Batch->getBlank();
        // ratio of Be10 / Be9
        $R_10to9_b = $blank->BeAms[0]->r_to_rstd * $blank->BeAms[0]->BeAmsStd->r10to9;
        // mass of Be in carrier added to the blank (in g)
        $M_cb = $blank->wt_be_carrier * 1e-6 * $bec->be_conc;
        $M_cb_err = $M_cb * $bec->del_be_conc * 1e-6;
        // error propagation for blank
        $n10_b_err_terms = array(
            // error from blank AMS measurement
            $blank->BeAms[0]->exterror * $M_cb * AVOGADRO / MM_BE,
            // error from carrier concentration
            $M_cb_err * $R_10to9_b * AVOGADRO / MM_BE,
        );
        $n10_b_err = sqrt(sum_of_squares($n10_b_err_terms));
        
        // Be10/Be9 ratio of the sample
        $R_10to9 = $BeAMS->r_to_rstd * $BeAMS->BeAmsStd->r10to9;
        // mass of the quartz in the sample
        $M_qtz = $this->wt_diss_bottle_sample - $this->wt_diss_bottle_tare;
        // Mass of Be added as carrier (grams)
        // Concentration is converted from ug.
        $M_c = $this->wt_be_carrier * 1e-6 * $bec->be_conc;
        $M_c_err =  $M_c * $bec->del_be_conc * 1e-6;
        // Estimate of Be10 concentration of sample
        $be10_conc = ($R_10to9 * $M_c - $R_10to9_b * $M_cb) * AVOGADRO / ($M_qtz * MM_BE);
        // Calculate the error in Be10 concentration ($be10_conc):
        // First, define the differentials for each error source. Each is
        // equivalent to del(Number of Be10 atoms)/del(source variable)
        // multiplied by the error in the source variable.
        $err_terms = array(
            $BeAMS->exterror * $M_c * AVOGADRO / ($M_qtz * MM_BE), // from ams error
            -$n10_b_err / $M_qtz, // from blank error
            $M_c_err * $R_10to9 * AVOGADRO / ($M_qtz * MM_BE), // from carrier error
        );
        $be10_err = sqrt(sum_of_squares($err_terms));

        return array($be10_conc, $be10_err);
    }
    
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

        return mean($vals);
    }
    
    public function getSolnWt()
    {
        return $this->wt_diss_bottle_total - $this->wt_diss_bottle_tare;
    }
    
    public function getSampleWt()
    {
        return $this->wt_diss_bottle_sample - $this->wt_diss_bottle_tare;
    }

}
<?php

/**
 * BeAms
 * 
 */
class BeAms extends BaseBeAms
{

    public function getConcBe10($useIcpBe=false)
    {
        $an = $this->Analysis; // the associated analysis
        $bec = $an->Batch->BeCarrier; // our Be carrier
        $blank = $an->Batch->getBlank(); // the blank in the batch

        // ratio of Be10 / Be9
        $R_10to9_b = $blank->BeAms[0]->r_to_rstd * $blank->BeAms[0]->BeAmsStd->r10to9;
        // mass of Be in carrier added to the blank (in g)
        $M_cb = $blank->wt_be_carrier * 1e-6 * $bec->be_conc;
        $M_cb_err = $M_cb * $bec->del_be_conc * 1e-6;
        // error propagation for blank
        $n10_b_err_terms = array(
            // error from blank AMS measurement uncertainty
            $blank->BeAms[0]->exterror * $M_cb * AVOGADRO / MM_BE,
            // error from carrier concentration uncertainty
            $M_cb_err * $R_10to9_b * AVOGADRO / MM_BE,
        );
        $n10_b_err = sqrt(sum_of_squares($n10_b_err_terms));

        // Be10/Be9 ratio of the sample
        $R_10to9 = $this->r_to_rstd * $this->BeAmsStd->r10to9;
        // mass of the quartz in the sample
        $M_qtz = $an->getSampleWt();
        // Mass of Be added as carrier (grams)
        // Concentration is converted from ug.
        $M_c = $an->wt_be_carrier * 1e-6 * $bec->be_conc;
        $M_c_err = $M_c * $bec->del_be_conc * 1e-6;
        // Estimate of Be10 concentration of sample
        $be10_conc = ($R_10to9 * $M_c - $R_10to9_b * $M_cb) * AVOGADRO / ($M_qtz * MM_BE);
        // Calculate the error in Be10 concentration ($be10_conc):
        // First, define the differentials for each error source. Each is
        // equivalent to del(Number of Be10 atoms)/del(source variable)
        // multiplied by the error in the source variable.
        $err_terms = array(
            $this->exterror * $M_c * AVOGADRO / ($M_qtz * MM_BE), // from ams error
            -$n10_b_err / $M_qtz, // from blank error
            $M_c_err * $R_10to9 * AVOGADRO / ($M_qtz * MM_BE), // from carrier error
        );
        $be10_err = sqrt(sum_of_squares($err_terms));

        return array($be10_conc, $be10_err);
    }

}
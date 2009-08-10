<?php

/**
 * Represents a row in the sample table in the database.
 */
class Sample extends BaseSample
{
    /**
     * Get an array with entries for each analysis and each analysis can have multiple
     * AMS results array(array(ams_result_text, ams_result_text, ...), array(...), ...).
     * Calculates
     * @return array(array(string)) $an_text
     */
     public function getCalcInputs()
     {
         $ero_text = $exp_text = array(array());
         $offset = 0;
         foreach ($this->Analysis as $an) {
             foreach($an->BeAms as $ams) {
                 if (!isset($ams->BeAmsStd) || !isset($ams->BeAmsStd->BeStdSeries)) {
                     // we don't have a standard, no sense in even showing it
                     continue;
                 }

                 if ($this->antarctic) {
                     $pressure_flag = 'ant';
                 } else {
                     $pressure_flag = 'std';
                 }

                 $bec = $an->Batch->BeCarrier;
                 $alc = $an->Batch->AlCarrier;

                 // calculate Be10 concentration and its error
                 // these calculations are based on:
                 // Converting Al and Be isotope ratio measurements to nuclide 
                 // concentrations in quartz.
                 // Greg Balco
                 // May 8, 2006
                 // http://hess.ess.washington.edu/math/docs/common/ams_data_reduction.pdf

                 // first find our blank
                 foreach ($an->Batch->Analysis as $tmpa) {
                     if ($tmpa->sample_type == 'BLANK') {
                         $blank = $tmpa;
                         break;
                     }
                 }

                 // we now need to estimate the Be10 concentration of the blank if found
                 $M_b = 0;
                 $M_b_err = 0;
                 if (isset($blank)) {
                     if (isset($blank->BeAms) && isset($blank->BeAms->BeAmsStd) 
                             && isset($blank->BeAms->BeAmsStd->BeStdSeries)) {
                         $R_10to9_b = $blank->BeAms->r_to_rstd * $blank->BeAms->BeAmsStd->r10to9;
                         $M_c_b = $blank->wt_be_carrier * $bec->be_conc * 1e-6;
                         $M_c_b_err = $bec->del_be_conc * 1e-6 * $M_c_b;
                         $M_b = $R_10to9_b * $M_c_b * AVOGADRO / MM_BE;
                         $blk_err_terms = array(
                             $blank->BeAms->exterror * $M_c_b * AVOGADRO / MM_BE,
                             $M_c_b_err * $R_10to9_b * AVOGADRO / MM_BE);
                         $M_b_err = sqrt(sum_of_squares($blk_err_terms));
                     }
                 }

                 // Be10/Be9 ratio of the sample
                 $R_10to9 = $ams->r_to_rstd * $ams->BeAmsStd->r10to9;
                 // mass of Be in the sample initially
                 $M_qtz = $an->wt_diss_bottle_sample - $an->wt_diss_bottle_tare;

                 // mass of Be added as carrier (grams)
                 // concentration is converted from ug
                 $M_c = $an->wt_be_carrier * 1e-6 * $bec->be_conc;
                 $M_c_err = $bec->del_be_conc * 1e-6 * $M_c;

                 // estimate of Be10 concentration of sample
                 $be10_conc = (($R_10to9 * $M_c * AVOGADRO / MM_BE) - $M_b) / $M_qtz;

                 // Calculate the error in Be10 concentration ($be10_conc):
                 // First, define the differentials for each error source. Each is
                 // equivalent to del(Number of Be10 atoms)/del(source variable)
                 // multiplied by the error in the source variable.
                 $err_terms = array(
                     $ams->exterror * $M_c * AVOGADRO / $M_qtz * MM_BE, // from ams error
                     $M_b_err * (-1 / $M_qtz), // from blank error
                     $M_c_err * ($R_10to9 * AVOGADRO / $M_qtz * MM_BE), // from carrier error
                 ); 
                 $be10_err = sqrt(sum_of_squares($err_terms));

                 // we default to a zero aluminum analysis
                 if (!isset($this->Analysis->AlAms)) {
                     $al26_conc = $al26_err = 0;
                     $al26_code = 'KNSTD';
                 } else {
                     // calculate aluminum concentration and error
                     // temporary settings until we work out this calculation
                     $al26_conc = $al26_err = 0;
                     $al26_code = 'KNSTD';
                 }

                 $entries = array(
                     substr($this->name, 0, 24),
                     $this->latitude,
                     $this->longitude,
                     $this->altitude,
                     $pressure_flag,
                     abs($this->depth_bottom - $this->depth_top),
                     $this->density,
                     $this->shield_factor,
                     $this->erosion_rate,
                     $be10_conc,
                     $be10_err,
                     $ams->BeAmsStd->BeStdSeries->code,
                     $al26_conc,
                     $al26_err,
                     $al26_code,
                 );

                 $text = '';
                 foreach ($entries as $ent) {
                     $text .= ' ' . $ent;
                 }
                 $exp_text[$offset][] = trim($text);

                 unset($entries[8]); // remove erosion rate estimate

                 $text = '';
                 foreach ($entries as $ent) {
                     $text .= ' ' . $ent;
                 }
                 $ero_text[$offset][] = trim($text);
              }
              ++$offset;
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
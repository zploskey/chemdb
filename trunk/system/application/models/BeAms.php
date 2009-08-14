<?php

/**
 * BeAms
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
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
	
	public function __construct()
	{
		parent::__construct();
		$this->ageCalcInput = null;
		$this->erosCalcInput = null;
	}
	
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
	public function getAgeCalcInput()
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
		$bec = $an->Batch->BeCarrier;
		$alc = $an->Batch->AlCarrier;
		
		if ($sample->antarctic) {
		    $pressure_flag = 'ant';
		} else {
		    $pressure_flag = 'std';
		}
		
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
		$R_10to9 = $this->r_to_rstd * $this->BeAmsStd->r10to9;
		// Mass of Be in the sample initially
		$M_qtz = $an->wt_diss_bottle_sample - $an->wt_diss_bottle_tare;

		// Mass of Be added as carrier (grams)
		// Concentration is converted from ug.
		$M_c = $an->wt_be_carrier * 1e-6 * $bec->be_conc;
		$M_c_err = $bec->del_be_conc * 1e-6 * $M_c;

		// Estimate of Be10 concentration of sample
		$be10_conc = (($R_10to9 * $M_c * AVOGADRO / MM_BE) - $M_b) / $M_qtz;

		// Calculate the error in Be10 concentration ($be10_conc):
		// First, define the differentials for each error source. Each is
		// equivalent to del(Number of Be10 atoms)/del(source variable)
		// multiplied by the error in the source variable.
		$err_terms = array(
		    $this->exterror * $M_c * AVOGADRO / $M_qtz * MM_BE, // from ams error
		    $M_b_err * (-1 / $M_qtz), // from blank error
		    $M_c_err * ($R_10to9 * AVOGADRO / $M_qtz * MM_BE), // from carrier error
		); 
		$be10_err = sqrt(sum_of_squares($err_terms));

		// we default to a zero aluminum analysis
		if (!isset($an->AlAms)) {
		    $al26_conc = $al26_err = 0;
		    $al_calc_code = 'KNSTD';
		} else {
		    // calculate aluminum concentration and error
		    // temporary settings until we work out this calculation
			list($al26_conc, $al26_err) = $an->AlAms->getConc();
		    $al_calc_code = $an->AlAms->AlAmsStd->AlStdSeries->code;
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
		$this->ageCalcInput = trim($text);
		
		// generate erosion rate input
		unset($entries['erosion_rate']); // remove erosion rate estimate
		$text = '';
		foreach ($entries as $ent) {
		    $text .= $ent . ' ';
		}
		$this->erosCalcInput = trim($text);		
		return $this->ageCalcInput;
	}
	
	/**
	 * Gets the input to the CRONUS calculator for calculating an erosion rate.
	 *
	 * @return string $erosCalcInput
	 **/
	public function getErosCalcInput()
	{
		if (isset($this->erosCalcInput) && !$this->isModified(true)) {
			return $this->erosCalcInput;
		}
		
		$this->getAgeCalcInput(); // will set erosCalcInput as a side-effect
		return $this->erosCalcInput;
	}
}
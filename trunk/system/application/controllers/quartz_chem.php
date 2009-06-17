<?php
/**
 * Business logic for the quartz chemistry pages.
 * 
 * Methods appear in the same order as they are listed in the front page for
 * the Quartz Chemistry section.
 */

class Quartz_chem extends MY_Controller
{
	/**
	 * @var Doctrine_Table batch table
	 */
	var $batch;

	/**
	 * @var Doctrine_Table Beryllium carrier table
	 */
	var $be_carrier;

	/**
	 * @var Doctrine_Table Aluminum carrier table
	 */
	var $al_carrier;

	/**
	 * Contructs the class object, connects to database, and loads necessary
	 * libraries.
	 **/
	function Quartz_chem()
	{
		parent::MY_Controller();
		$this->batch = Doctrine::getTable('Batch');
		//$this->analysis = Doctrine::getTable('Analysis');
		$this->be_carrier = Doctrine::getTable('BeCarrier');
		$this->al_carrier = Doctrine::getTable('AlCarrier');
	}
	
	/**
	 * The main quartz chemistry page. Contains a series of select boxes and
	 * links to the other pages.
	 */
	function index()
	{
		// build option tags for the select boxes
		foreach ($this->batch->findOpenBatches() as $b) {
			$data->open_batches .= "<option value=$b->id>$b->start_date $b->owner $b->description";
		}
		
		foreach ($this->batch->findAllBatches() as $b) {
			$data->all_batches .= "<option value=$b->id>$b->start_date $b->owner $b->description";
		}
		
		$data->title = 'Quartz Al-Be chemistry';
		$data->subtitle = 'Al-Be extraction from quartz:';
		$data->main = 'quartz_chem/index';
		$this->load->view('template', $data);
	}

	/**
	 * Form for adding a batch or editing the batch information.
	 */
	function new_batch()
	{
		$id = $this->input->post('id');
		$is_edit = (bool) $id;
		$data->allow_num_edit = ( ! $is_edit);
		
		if ($is_edit) {
			// it's an existing batch, get it
			$batch = $this->batch->find($id);
			
			if ( ! $batch) {
				show_404('page');
			}

			$data->numsamples = $this->batch->findNumSamples($id);

		} else {
			// it's a new batch
			$batch = new Batch();
			// number of samples is not known
			$data->numsamples = null;
		}

		if ($this->form_validation->run('batches') == FALSE) {
			// validation failed, redisplay the form
			if ($is_edit) {
				$batch->id = set_value('id', $batch->id);
				$batch->owner = set_value('owner', $batch->owner);
				$batch->description = set_value('description', $batch->description);
				$batch->start_date = set_value('start_date', $batch->start_date);
			} else {
				$batch->id = set_value('id');
				$batch->owner = set_value('owner');
				$batch->description = set_value('description');
				$batch->start_date = date('Y-m-d');
			}
		} else {
			// inputs are valid
			// grab batch info from post and save it to the db
			$fields = array('id', 'owner', 'description', 'start_date');
			foreach ($fields as $f) {
				$batch->{$f} = $this->input->post($f);
			}
			
			$new_batch = ( ! $batch->id);

			$batch->save();
			
			if ($new_batch) {
				// new batch: create the analyses linked to this batch
				$numsamples = $this->input->post('numsamples');

				$conn = Doctrine_Manager::connection();
				$conn->beginTransaction();
				for ($i = 0; $i < $numsamples; $i++) {
					$analysis = new Analysis();
					$analysis->batch_id = $batch->id;
					$analysis->number_within_batch = $i + 1;
					$analysis->save();
				}
				$conn->commit();

				//$this->analysis->insertStubs($batch->id, $numsamples);
			}
		}

		// set the rest of the view data
		$data->numsamples = $this->input->post('numsamples');
		$data->title = 'Add a batch';
		$data->main = 'quartz_chem/new_batch';
		$data->batch = $batch;
		$this->load->view('template', $data);
	}
	
	/**
	 * Shows the sample loading page.
	 * @return void
	 **/
	function load_samples()
	{
		$is_refresh = (bool) $this->input->post('is_refresh');
		$batch_id = $this->input->post('batch_id');

		// grab the batch
		$batch = Doctrine_Query::create()
			->from('Batch b')
			->leftJoin('b.Analysis a')
			->leftJoin('b.BeCarrier bec')
			->leftJoin('b.AlCarrier alc')
			->where('b.id = ?', $batch_id)
			->fetchOne(); 
	/*
		if ( ! $batch) {
			echo 'Batch query failed.';
		} */

		$num_analyses = $batch->Analysis->count();

		// create the lists of carrier options
		foreach ($this->be_carrier->getList() as $c) {
			$data->be_carrier_options .= "<option value=$c->id ";
			if ($batch->BeCarrier != null AND $c->id == $batch->BeCarrier->id) {
				$data->be_carrier_options .= 'selected';
			}
			$data->be_carrier_options .= "> $c->name";
		}

		foreach($this->al_carrier->getList() as $c) {
			$data->al_carrier_options .= "<option value=$c->id ";
			if ($batch->BeCarrier != null AND $c->id == $batch->AlCarrier->id) {
				$data->al_carrier_options .= 'selected';
			}
			$data->al_carrier_options .= "> $c->name";
		}

		// create diss bottle dropdown options for each sample
		$diss_bottles = Doctrine_Query::create()
			->from('DissBottle d')
			->execute();

		// initialize carrier total weights
		$be_tot_wt = 0;
		$al_tot_wt = 0;

		$diss_bottle_options = array();
		$prechecks = array();
		for($i = 0; $i < count($batch->Analysis); $i++) {
			$html = '';
			foreach($diss_bottles as $bottle) {
				$html .= '<option value='.$bottle->id;
				if ($bottle->id == $batch->Analysis[$i]->diss_bottle_id) {
					$html .= ' selected';
				}
				$html .= "> $bottle->bottle_number";
			}
			$diss_bottle_options[$i] = $html;

			$temp_sample_wt = $batch->Analysis[$i]->wt_diss_bottle_sample -
				$batch->Analysis[$i]->wt_diss_bottle_tare;

			// get cations while we're at it
			$precheck = Doctrine_Query::create()
				->from('AlcheckAnalysis a')
				->leftJoin('a.AlcheckBatch b')
				->select('a.icp_al, a.icp_fe, a.icp_ti, a.wt_bkr_tare, a.wt_bkr_sample, a.wt_bkr_soln, b.prep_date')
			//	->where('a.sample_name = ?', $batch->Analysis[$i]->sample_name)
				->where('a.alcheck_batch_id = ?', $batch->id)
				->orderBy('b.prep_date DESC')
				->limit(1)
				->fetchOne();

			if($precheck) {
				// there's data, calculate the concentrations
				$prechecks[$i]['show'] = TRUE;
				if ($precheck['wt_bkr_sample'] == 0 AND $precheck['wt_bkr_tare'] == 0) {
					$temp_df = 0;
				} else {
					$temp_df = ($precheck['wt_bkr_soln'] - $precheck['wt_bkr_tare'])
							/ ($precheck['wt_bkr_sample'] - $precheck['wt_bkr_tare']);
				}
				$temp_al = $precheck['icp_al'] * $temp_df * $temp_sample_wt / 1000;
				$temp_fe = $precheck['icp_fe'] * $temp_df * $temp_sample_wt / 1000;
				$temp_ti = $precheck['icp_ti'] * $temp_df * $temp_sample_wt / 1000;
				$temp_tot_al = $temp_al + ($batch->Analysis[$i]->wt_al_carrier
					* $batch->AlCarrier->al_conc) / 1000;
				
				$prechecks[$i]['conc_al'] = sprintf('%.1f', $precheck['icp_al'] * $temp_df);
				$prechecks[$i]['conc_fe'] = sprintf('%.1f', $precheck['icp_fe'] * $temp_df);
				$prechecks[$i]['conc_ti'] = sprintf('%.1f', $precheck['icp_ti'] * $temp_df);
			} else {
				$prechecks[$i]['show'] = FALSE;
				if ($batch->Analysis[$i]->wt_al_carrier > 0) {
					$temp_tot_al = $batch->Analysis[$i]->wt_al_carrier * $batch->AlCarrier->al_conc / 1000;
				} else {
					$temp_tot_al = '--';
				}
				$temp_fe = '--';
				$temp_ti = '--';
			}

			$prechecks[$i]['m_al'] = $temp_tot_al;
			$prechecks[$i]['m_fe'] = $temp_fe;
			$prechecks[$i]['m_ti'] = $temp_ti;

			$be_tot_wt += $batch->Analysis[$i]->wt_be_carrier;
			$al_tot_wt += $batch->Analysis[$i]->wt_al_carrier;

		}

		// get previous carrier weights
		$data->be_prev = $this->batch->findPrevBeCarrierWt($batch->BeCarrier->id, $batch->start_date);
		$data->al_prev = $this->batch->findPrevAlCarrierWt($batch->AlCarrier->id, $batch->start_date);

		$errors = FALSE;

		// if this is a refresh we need to validate the data
		if ($is_refresh) {
			$is_valid = $this->form_validation->run('load_samples');

			// grab the submitted data
			$batch->merge($this->input->post('batch'));
			$batch->id = $batch_id;

			if ($is_valid) {
				// valid info, save changes to the database and reload
				$batch->save();
				$_POST['is_refresh'] = FALSE;
				redirect('quartz_chem/load_samples');
			} else {
				// validation failed
				$errors = TRUE;
			}

		} else {
			// it was not a refresh, reset fields from the database
			$data->batch = $batch;
		}

		// perform remaining calculations for displaying
		$data->be_diff_wt = $batch->wt_be_carrier_init - $batch->wt_be_carrier_final;
		$data->al_diff_wt = $batch->wt_al_carrier_init - $batch->wt_al_carrier_final;
		$data->be_tot_wt = $be_tot_wt;
		$data->al_tot_wt = $al_tot_wt;
		$data->be_diff = $data->be_diff_wt - $data->be_tot_wt;
		$data->al_diff = $data->al_diff_wt - $data->al_tot_wt;

		$data->prechecks = $prechecks;

		$data->diss_bottle_options = $diss_bottle_options;
		$data->num_analyses = $num_analyses;
		$data->title = 'Sample weighing and carrier addition';
		$data->main = 'quartz_chem/load_samples';
		$this->load->view('template', $data);
	}

	function print_tracking_sheet() {
		$batch_id = $this->input->post('batch_id');
		$batch = Doctrine_Query::create()
			->from('Batch b')
			->leftJoin('b.Analysis a')
			->leftJoin('b.AlCarrier ac')
			->leftJoin('b.BeCarrier bc')
            ->leftJoin('a.DissBottle db')
			->where('b.id = ?', $batch_id)
            ->limit(1)
			->fetchOne();

		for ($i = 0; $i < $batch->Analysis->count(); $i++) {

			$precheck = Doctrine_Query::create()
				->from('AlcheckAnalysis a')
				->leftJoin('a.AlcheckBatch b')
				->select('a.sample_name, a.icp_al, a.icp_fe, a.icp_ti, a.wt_bkr_tare, a.wt_bkr_sample, a.wt_bkr_soln, b.prep_date')
				->where('a.sample_name = ?', $batch->Analysis[$i]->sample_name)
                ->andWhere('a.alcheck_batch_id = b.id')
				->orderBy('b.prep_date DESC')
				->limit(1)
				->fetchOne();

			$tmpa[$i]['tmpSampleWt'] = $batch->Analysis[$i]->wt_diss_bottle_sample
                - $batch->Analysis[$i]->wt_diss_bottle_tare;
			$tmpa[$i]['mlHf'] = round($tmpa[$i]['tmpSampleWt']) * 5 + 5;

			$tmpa[$i]['inAlDb'] = TRUE;
			if ($precheck) {
				$sampleWt = ($precheck['wt_bkr_sample'] - $precheck['wt_bkr_tare']);
				if ($sampleWt != 0) {
					$temp_df = ($precheck['wt_bkr_soln'] - $precheck['wt_bkr_tare']) / $sampleWt;
				} else {
					$temp_df = 0;
				}

				$temp_al = $precheck['icp_al'] * $temp_df * $tmpa[$i]['tmpSampleWt'] / 1000;
				$tmpa[$i]['tot_fe'] = sprintf('%.2f', $precheck['icp_fe'] * $temp_df * $tmpa[$i]['tmpSampleWt'] / 1000);
				$tmpa[$i]['tot_ti'] = sprintf('%.2f', $precheck['icp_ti'] * $temp_df * $tmpa[$i]['tmpSampleWt'] / 1000);
				$tmpa[$i]['tot_al'] = sprintf('%.2f', $temp_al + ($batch->Analysis[$i]->wt_al_carrier * $batch->AlCarrier->al_conc) / 1000);
			} elseif ($batch->Analysis[$i]->sample_type == 'BLANK') {
				if ($batch->Analysis[$i]->wt_al_carrier > 0) {
					$tmpa[$i]['tot_al'] = sprintf('%.2f', ($batch->Analysis[$i]->wt_al_carrier * $batch->AlCarrier->al_conc) / 1000);
				}
				else {
					$tmpa[$i]['tot_al'] = ' -- ';
				}
				$tmpa[$i]['tot_ti'] = ' -- ';
				$tmpa[$i]['tot_fe'] = ' -- ';
			} else {
				// sample isn't in the Alchecks database
				$tmpa[$i]['inAlDb'] = FALSE;
			}
		}
		
		$data->batch = $batch;
        $data->tmpa = $tmpa;
		$this->load->view('quartz_chem/print_tracking_sheet', $data);
	}

    function add_solution_weights() {
        $batch_id = (int)$this->input->post('batch_id');
        $is_refresh = (bool)$this->input->post('is_refresh');

        $batch = Doctrine_Query::create()
            ->from('Batch b')
            ->leftJoin('b.Analysis a')
            ->leftJoin('a.DissBottle db')
            ->where('b.id = ?', $batch_id)
            ->limit(1)
            ->fetchOne();
        
        if ($is_refresh) {
            $weights = $this->input->post('wt_diss_bottle_total');
            $count = count($weights);
            for ($i = 0; $i < $count; $i++) {
                $batch->Analysis[$i]->wt_diss_bottle_total = $weights[$i];
            }
        }

        $errors = FALSE;
        if ($is_refresh) {
            $is_valid = $this->form_validation->run('add_solution_weights');
            if ($is_valid) {
                $batch->save();
            } else {
                $errors = TRUE;
            }
        }
        
        $data->errors = $errors;
        $data->numsamples = $batch->Analysis->count();
		$data->title = 'Add total solution weights';
		$data->main = 'quartz_chem/add_solution_weights';
		$data->batch = $batch;
		$this->load->view('template', $data);
    }

    function 
}

/* End of file quartz_chem.php */
/* Location: ./system/application/controllers/quartz_chem.php */

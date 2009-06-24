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
     * @var Doctrine_Table Split beaker table
     */
     var $split_bkr;

	/**
	 * Contructs the class object, connects to database, and loads necessary
	 * libraries.
	 **/
	function Quartz_chem()
	{
		parent::MY_Controller();
		$this->batch = Doctrine::getTable('Batch');
		$this->be_carrier = Doctrine::getTable('BeCarrier');
		$this->al_carrier = Doctrine::getTable('AlCarrier');
        $this->split_bkr = Doctrine::getTable('SplitBkr');
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
		$id = $this->input->post('batch_id');
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
            $batch->merge($this->input->post('batch'));
			
			$new_batch = ( ! ((bool)$batch->id));

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
		$is_refresh = (bool)$this->input->post('is_refresh');
		$batch_id = $this->input->post('batch_id');

		// grab the batch
		$batch = Doctrine_Query::create()
			->from('Batch b')
			->leftJoin('b.Analysis a')
			->leftJoin('b.BeCarrier bec')
			->leftJoin('b.AlCarrier alc')
			->where('b.id = ?', $batch_id)
            ->fetchOne();

		if ( ! $batch) {
			die('Batch query failed.');
		} 

		$num_analyses = $batch->Analysis->count();
		$errors = FALSE;

		// if this is a refresh we need to validate the data
		if ($is_refresh) {
			$is_valid = $this->form_validation->run('load_samples');

			// grab the submitted data
			$batch->merge($this->input->post('batch'));
			$batch->id = $batch_id;

            $sample_name = $this->input->post('sample_name');
            $sample_type = $this->input->post('sample_type');
            $diss_bottle_id = $this->input->post('diss_bottle_id');
            $wt_diss_bottle_tare = $this->input->post('wt_diss_bottle_tare');
            $wt_diss_bottle_sample = $this->input->post('wt_diss_bottle_sample');
            $wt_be_carrier = $this->input->post('wt_be_carrier');
            $wt_al_carrier = $this->input->post('wt_al_carrier');

            for ($a = 0; $a < $num_analyses; $a++) { // analysis loop
                $batch->Analysis[$a]->sample_name = $sample_name[$a];
                $batch->Analysis[$a]->sample_type = $sample_type[$a];
                $batch->Analysis[$a]->diss_bottle_id = $diss_bottle_id[$a];
                $batch->Analysis[$a]->wt_diss_bottle_tare = $wt_diss_bottle_tare[$a];
                $batch->Analysis[$a]->wt_diss_bottle_sample = $wt_diss_bottle_sample[$a];
                $batch->Analysis[$a]->wt_be_carrier = $wt_be_carrier[$a];
                $batch->Analysis[$a]->wt_al_carrier = $wt_al_carrier[$a];
            }

			if ($is_valid) {
				// valid info, save changes to the database and reload
				$batch->save();
			} else {
				// validation failed
				$errors = TRUE;
			}
		}

        // create the lists of carrier options
        $be_list = $this->be_carrier->getList();
        $al_list = $this->al_carrier->getList();

		foreach ($be_list as $c) {
			$data->be_carrier_options .= "<option value=$c->id ";
			if ($batch->BeCarrier != null AND $c->id == $batch->BeCarrier->id) {
				$data->be_carrier_options .= 'selected';
			}
			$data->be_carrier_options .= "> $c->name";
		}

		foreach($al_list as $c) {
			$data->al_carrier_options .= "<option value=$c->id ";
			if ($batch->BeCarrier != null AND $c->id == $batch->AlCarrier->id) {
				$data->al_carrier_options .= 'selected';
			}
			$data->al_carrier_options .= "> $c->name";
		}

		// initialize carrier weights and Alcheck data arrays
		$be_tot_wt = 0;
		$al_tot_wt = 0;
		$diss_bottle_options = array();
		$prechecks = array();

        // get all the dissolution bottle numbers
		$diss_bottles = Doctrine_Query::create()
			->from('DissBottle d')
			->execute();

		for($i = 0; $i < $num_analyses; $i++) {
            // create diss bottle dropdown options for each sample
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
				->from('AlcheckAnalysis a, a.AlcheckBatch b')
				->select('a.icp_al, a.icp_fe, a.icp_ti, a.wt_bkr_tare, a.wt_bkr_sample, '
                    .'a.wt_bkr_soln, b.prep_date')
				->where('a.analysis_id = ?', $batch->Analysis[$i]->id)
				->orderBy('b.prep_date DESC')
				->limit(1)
                ->fetchOne();

			if($precheck AND $batch->AlCarrier) {
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
				$temp_fe = '--';
				$temp_ti = '--';
				$temp_tot_al = '--';

				if ($batch->AlCarrier AND $batch->Analysis[$i]->wt_al_carrier > 0) {
                    $temp_tot_al = $batch->Analysis[$i]->wt_al_carrier
                        * $batch->AlCarrier->al_conc / 1000;
				}
			}

			$prechecks[$i]['m_al'] = $temp_tot_al;
			$prechecks[$i]['m_fe'] = $temp_fe;
			$prechecks[$i]['m_ti'] = $temp_ti;

			$be_tot_wt += $batch->Analysis[$i]->wt_be_carrier;
			$al_tot_wt += $batch->Analysis[$i]->wt_al_carrier;
		}

		// get previous carrier weights
		if ($batch->BeCarrier) {
			$data->be_prev = $this->batch->findPrevBeCarrierWt($batch->id, $batch->be_carrier_id);
		}
		if ($batch->AlCarrier) {
			$data->al_prev = $this->batch->findPrevAlCarrierWt($batch->id, $batch->al_carrier_id);
		}

		// set display variables
        $data->batch = $batch;
        $data->num_analyses = $num_analyses;
        $data->prechecks = $prechecks;
		$data->diss_bottle_options = $diss_bottle_options;
        $data->errors = $errors;
        
        // and calculated metadata for display
		$data->be_diff_wt = $batch->wt_be_carrier_init - $batch->wt_be_carrier_final;
		$data->al_diff_wt = $batch->wt_al_carrier_init - $batch->wt_al_carrier_final;
        $data->be_diff = $data->be_diff_wt - $be_tot_wt;
		$data->al_diff = $data->al_diff_wt - $al_tot_wt;
        $data->be_tot_wt = $be_tot_wt;
		$data->al_tot_wt = $al_tot_wt;

        // template info
		$data->title = 'Sample weighing and carrier addition';
		$data->main = 'quartz_chem/load_samples';
		$this->load->view('template', $data);
	}
    
	function print_tracking_sheet()
	{
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

        $numsamples = $batch->Analysis->count();

		for ($i = 0; $i < $numsamples; $i++) {
			$precheck = Doctrine_Query::create()
				->from('AlcheckAnalysis a')
				->leftJoin('a.AlcheckBatch b')
				->select('a.sample_name, a.icp_al, a.icp_fe, a.icp_ti, a.wt_bkr_tare, '
                    .'a.wt_bkr_sample, a.wt_bkr_soln, b.prep_date')
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
				$tmpa[$i]['tot_fe'] = sprintf('%.2f',
                    $precheck['icp_fe'] * $temp_df * $tmpa[$i]['tmpSampleWt'] / 1000);
				$tmpa[$i]['tot_ti'] = sprintf('%.2f',
                    $precheck['icp_ti'] * $temp_df * $tmpa[$i]['tmpSampleWt'] / 1000);
				$tmpa[$i]['tot_al'] = sprintf('%.2f', $temp_al
                    + ($batch->Analysis[$i]->wt_al_carrier * $batch->AlCarrier->al_conc) / 1000);
			} elseif (strcmp($batch->Analysis[$i]->sample_type, 'BLANK') == 0) {
				if ($batch->Analysis[$i]->wt_al_carrier > 0) {
					$tmpa[$i]['tot_al'] = sprintf('%.2f',
                        ($batch->Analysis[$i]->wt_al_carrier * $batch->AlCarrier->al_conc) / 1000);
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

    function add_solution_weights() 
    {
        $batch_id = (int)$this->input->post('batch_id');
        $is_refresh = (bool)$this->input->post('is_refresh');

        $batch = Doctrine_Query::create()
            ->from('Batch b')
            ->leftJoin('b.Analysis a')
            ->leftJoin('a.DissBottle db')
            ->where('b.id = ?', $batch_id)
            ->limit(1)
            ->fetchOne();

        $errors = FALSE;
        if ($is_refresh) {
            $batch->merge($this->input->post('batch'));

            $weights = $this->input->post('wt_diss_bottle_total');
            $count = count($weights);

            for ($i = 0; $i < $count; $i++) {
                $batch->Analysis[$i]->wt_diss_bottle_total = $weights[$i];
            }

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

    function add_split_weights() 
    {
        $batch_id = (int)$this->input->post('batch_id');
        $is_refresh = (bool)$this->input->post('is_refresh');
        
        $batch = Doctrine_Query::create()
            ->from('Batch b')
            ->leftJoin('b.Analysis a')
            ->leftJoin('a.DissBottle db')
            ->leftJoin('a.Split s')
            ->leftJoin('s.SplitBkr sb')
            ->where('b.id = ?', $batch_id)
            ->limit(1)
            ->fetchOne();

        $numsamples = $batch->Analysis->count();

        $errors = FALSE;
        if ($is_refresh) {
            // change the object data
            $batch->merge($this->input->post('batch'));

            $bkr_ids = $this->input->post('split_bkr');
            $tares_wts = $this->input->post('bkr_tare');
            $split_wts = $this->input->post('bkr_split');
            
            $ind = 0; // index for post variable arrays
            for ($a = 0; $a < $numsamples; $a++) { // analysis loop
                $nsplits = $batch->Analysis[$a]->Split->count();
                for ($s = 0; $s < $nsplits; $s++, $ind++) { // split loop
                    $batch->Analysis[$a]->Split[$s]->split_bkr_id = $bkr_ids[$ind];
                    $batch->Analysis[$a]->Split[$s]->wt_split_bkr_tare = $tares_wts[$ind];
                    $batch->Analysis[$a]->Split[$s]->wt_split_bkr_split = $split_wts[$ind];
                }
            }

            // validate the form
            if ($this->form_validation->run('add_split_weights')) {
                $batch->save();
            } else {
                $errors = TRUE;
            }
        }

        $data->errors = $errors;
        $data->numsamples = $numsamples;
        $data->batch = $batch;
        $data->bkr_list = $this->split_bkr->getList();
		$data->title = 'Add split weights';
		$data->main = 'quartz_chem/add_split_weights';
		$this->load->view('template', $data);
    }

    function add_icp_weights() 
    {
        $batch_id = (int)$this->input->post('batch_id');
        $is_refresh = (bool)$this->input->post('is_refresh');

        $batch = Doctrine_Query::create()
            ->from('Batch b')
            ->leftJoin('b.Analysis a')
            ->leftJoin('a.DissBottle db')
            ->leftJoin('a.Sample sa')
            ->leftJoin('a.Split sp')
            ->leftJoin('sp.SplitBkr sb')
            ->where('b.id = ?', $batch_id)
            ->limit(1)
            ->fetchOne();

        $numsamples = $batch->Analysis->count();

        // For the case when it's a new icp run, change from the default to today's date.
        // It will be saved when valid data is submitted on the form.
        if (strcmp($batch->icp_date, '0000-00-00') == 0)  {
            $batch->icp_date = date('Y-m-d');
        }

        $errors = FALSE;
        if ($is_refresh) {
            // change the object data
            $batch->merge($this->input->post('batch'));
            $tot_wts = $this->input->post('tot_wts');
            $ind = 0; // index for post variable arrays
            for ($a = 0; $a < $numsamples; $a++) { // analysis loop
                $nsplits = $batch->Analysis[$a]->Split->count();
                for ($s = 0; $s < $nsplits; $s++, $ind++) { // split loop
                    $batch->Analysis[$a]->Split[$s]->wt_split_bkr_icp = $tot_wts[$ind];
                }
            }

            // validate the form
            if ($this->form_validation->run('add_split_weights')) {
                $batch->save();
            } else {
                $errors = TRUE;
            }
        }

        $data->errors = $errors;
        $data->numsamples = $numsamples;
        $data->batch = $batch;
		$data->title = 'Add ICP solution weights';
		$data->main = 'quartz_chem/add_icp_weights';
		$this->load->view('template', $data);
    }

    // --------
    // REPORTS:
    // --------

    function intermediate_report() 
    {
        $batch_id = $this->input->post('batch_id');
		$batch = Doctrine_Query::create()
			->from('Batch b')
			->leftJoin('b.Analysis a')
            ->leftJoin('a.Sample sa')
			->leftJoin('b.AlCarrier ac')
			->leftJoin('b.BeCarrier bc')
            ->leftJoin('a.DissBottle db')
            ->leftJoin('a.Split sp')
            ->leftJoin('sp.IcpRun run')
            ->leftJoin('sp.SplitBkr spb')
			->where('b.id = ?', $batch_id)
            ->limit(1)
			->fetchOne();

        $numsamples = $batch->Analysis->count();

        // Let's do some math for the derived weights
        $wt_be_carrier_disp = 0;
        $wt_al_carrier_disp = 0;
        
        $max_nsplits = 0;
        for ($i = 0 ; $i < $numsamples; $i++) {
            $analysis = $batch->Analysis[$i];
            
            $sample_name[$i] = $analysis->sample_name;
            $wt_sample[$i] = $analysis->wt_diss_bottle_sample - $analysis->wt_diss_bottle_tare;
            $wt_HF_soln[$i] = $analysis->wt_diss_bottle_total - $analysis->wt_diss_bottle_tare;
            $wt_al_carrier_disp += $analysis->wt_al_carrier;  
            $wt_be_carrier_disp += $analysis->wt_be_carrier;   
            $numsplits = $batch->Analysis[$i]->Split->count();
            $nsplits[$i] = $numsplits;
            
            if ($numsplits > $max_nsplits) {
                $max_nsplits = $numsplits;
            }
            
            for ($s = 0; $s < $numsplits; $s++) {
                $wt_split[$i][$s] = $analysis->Split[$s]->wt_split_bkr_split 
                    - $analysis->Split[$s]->wt_split_bkr_tare;
                $wt_icp[$i][$s] = $analysis->Split[$s]->wt_split_bkr_icp - $analysis->Split[$s]->wt_split_bkr_tare;
                if ($wt_split[$i][$s] > 0) {
                    $tot_df[$i][$s] = ($wt_icp[$i][$s] / $wt_split[$i][$s]) * $wt_HF_soln[$i];
                }
            }

            $wt_be[$i] = $analysis['wt_be_carrier'] * $batch->BeCarrier->be_conc;

            $wt_al_fromc[$i] = $analysis['wt_al_carrier'] * $batch->AlCarrier->al_conc;

            // Do the usual thing with the Al checks database --
            // Attempt to obtain Al/Fe/Ti concentrations

            $precheck = Doctrine_Query::create()
				->from('AlcheckAnalysis a')
				->leftJoin('a.AlcheckBatch b')
				->select('a.sample_name, a.icp_al, a.icp_fe, a.icp_ti, a.wt_bkr_tare, '
                    .'a.wt_bkr_sample, a.wt_bkr_soln, b.prep_date')
				->where('a.sample_name = ?', $batch->Analysis[$i]->sample_name)
                ->andWhere('a.alcheck_batch_id = b.id')
				->orderBy('b.prep_date DESC')
				->limit(1)
				->fetchOne();
            if ($precheck) {
                $al_check_df = ($precheck['wt_bkr_soln'] - $precheck['wt_bkr_tare'])
                    / ($precheck['wt_bkr_sample'] - $precheck['wt_bkr_tare']);
                $check_al = $precheck['icp_al'] * $al_check_df * $wt_sample[$i];
                $check_fe[$i] = $precheck['icp_fe'] * $al_check_df * $wt_sample[$i];
                $check_ti[$i] = $precheck['icp_ti'] * $al_check_df * $wt_sample[$i];
                $check_tot_al[$i] = $check_al + ($analysis['wt_al_carrier'] * $batch->AlCarrier->al_conc);
            } elseif (strcmp($analysis->sample_type, 'BLANK') == 0) {
                if ($analysis['wt_al_carrier'] > 0) {
                    $check_tot_al[$i] = ($analysis['wt_al_carrier'] * $batch->AlCarrier->al_conc);
                } else {
                    $check_tot_al[$i] = 0;
                }
                $check_Fe[$i] = 0;
                $check_Ti[$i] = 0;
            } else {
                $check_tot_al[$i] = 0;
                $check_fe[$i] = 0;
                $check_ti[$i] = 0;
            }

            for($s = 0; $s < $numsplits; $s++) {
                $temp_tot_al[] = 0;
                $temp_tot_be[] = 0;
                $n_al[] = 0;
                $n_be[] = 0;  
                $nRuns = $analysis->Split[$s]->IcpRun->count();
                for ($r = 0; $r < $nRuns; $r++) {
                    $icp_al[$i][$s][$r] = $analysis->Split[$s]->IcpRun[$r]->al_result;
                    $icp_be[$i][$s][$r] = $analysis->Split[$s]->IcpRun[$r]->be_result;
                    $icp_use_al[$i][$s][$r] = $analysis->Split[$s]->IcpRun[$r]->use_al;
                    $icp_use_be[$i][$s][$r] = $analysis->Split[$s]->IcpRun[$r]->use_be;
                    $al_tot[$i][$s][$r] = $icp_al[$i][$s][$r] * $tot_df[$i][$s];
                    $be_tot[$i][$s][$r] = $icp_be[$i][$s][$r] * $tot_df[$i][$s];

                    // record info for calculating the mean
                    if (strcmp($icp_use_al[$i][$s][$r], 'y') == 0) {
                        $temp_tot_al[$i] += $al_tot[$i][$s][$r];
                        $n_al[$i] += 1;
                    }
                    if (strcmp($icp_use_be[$i][$s][$r], 'y') == 0) {
                        $temp_tot_be[$i] += $be_tot[$i][$s][$r];
                        $n_be[$i] += 1;
                    }
                }
            }

            $al_tot_avg[$i] = $this->mean($temp_tot_al[$i], $n_al[$i]);
            $be_tot_avg[$i] = $this->mean($temp_tot_be[$i], $n_be[$i]);

            // calculate the Standard Deviation
            for($s = 0; $s < $numsplits; $s++) {
                $temp_sd_al[] = 0;
                $temp_sd_be[] = 0;
                $nRuns = $analysis->Split[$s]->IcpRun->count();
                for ($r = 0; $r < $nRuns; $r++) {
                    if (strcmp($icp_use_al[$i][$s][$r], 'y') == 0) {
                        $temp_sd_al[$i] += pow(($al_tot[$i][$s][$r] - $al_tot_avg[$i]), 2);
                    }
                    if (strcmp($icp_use_be[$i][$s][$r], 'y') == 0) {
                        $temp_sd_be[$i] += pow(($be_tot[$i][$s][$r] - $be_tot_avg[$i]), 2);
                    }
                }
            }

            if ($n_al[$i] > 1) {
                $al_tot_sd[$i] = sqrt($temp_sd_al[$i] / ($n_al[$i] - 1));
            } else {
                $al_tot_sd[$i] = 0;
            }

            if ($n_be[$i] > 1) {
                $be_tot_sd[$i] = sqrt($temp_sd_be[$i] / ($n_be[$i] - 1));
            } else {
                $be_tot_sd[$i] = 0;
            }

            // Calculate the percentage errors
            if ($al_tot_avg[$i] > 0) {
                $al_pct_sd[$i] = 100 * ($al_tot_sd[$i] / $al_tot_avg[$i]);
            } else {
                $al_pct_sd[$i] = 0;
            }
            
            if ($be_tot_avg[$i] > 0) {
                $be_pct_sd[$i] = 100 * ($be_tot_sd[$i] / $be_tot_avg[$i]);
            } else {
                $be_pct_sd[$i] = 0;
            }

            // Calculate the recoveries
            if ($wt_be[$i] > 0) {
                $be_recovery[$i] = 100 * $be_tot_avg[$i] / $wt_be[$i];
            } else {
                $be_recovery[$i] = 0;
            }
            if ($check_tot_al[$i] > 0) {
                $al_recovery[$i] = 100 * $al_tot_avg[$i] / $check_tot_al[$i];
            } else {
                $al_recovery[$i] = 0;
            }
        }

        $data->todays_date = date('Y-m-d');
        $data->max_nsplits = $max_nsplits;
        $data->numsamples = $numsamples;
        $data->nsplits = $nsplits;
        $data->batch = $batch;
        // carrier information
        $data->wt_be_carrier_diff = $batch->wt_be_carrier_init - $batch->wt_be_carrier_final;
        $data->wt_al_carrier_diff = $batch->wt_al_carrier_init - $batch->wt_al_carrier_final;
        $data->wt_be_carrier_disp = $wt_be_carrier_disp;
        $data->wt_al_carrier_disp = $wt_al_carrier_disp;
        // derived sample data
        $data->wt_sample = $wt_sample;
        $data->wt_HF_soln = $wt_HF_soln;
        $data->wt_be = $wt_be;
        $data->wt_al_fromc = $wt_al_fromc;
        $data->check_tot_al = $check_tot_al;
        $data->wt_split = $wt_split;
        $data->wt_icp = $wt_icp;
        $data->tot_df = $tot_df;
        
        $this->load->view('quartz_chem/intermediate_report', $data);
    }
    
    function add_icp_results() 
    {
        
        $data->batch = $batch;
        $this->load->view('quartz_chem/add_icp_results', $data);
    }
    
    /*
     * Helper function to calculate a mean without division by zero.
     * @param double total
     * @param int n, number of samples
     * @return double mean
     */
    function mean($total, $n) 
    {
        if ($n == 0) {
            return 0;
        } else {
            return $total / $n;
        }
    }

    // ----------
	// CALLBACKS:
	// ----------

    function valid_date($date_string) 
    {
        if ($this->form_validation->valid_date($date_string)) {
            return TRUE;
        }

        $this->form_validation->set_message('valid_date',
            'The %s field must be in the format YYYY-MM-DD.');
        return FALSE;
    }
}

/* End of file quartz_chem.php */
/* Location: ./system/application/controllers/quartz_chem.php */
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
     * Contructs the class object, connects to database, and loads necessary
     * libraries.
     **/
    function Quartz_chem()
    {
        parent::MY_Controller();
    }
    
    /**
     * The main quartz chemistry page. Contains a series of select boxes and
     * links to the other pages.
     */
    function index()
    {
        // build option tags for the select boxes
        foreach (Doctrine::getTable('Batch')->findOpenBatches() as $b) {
            $data->open_batches .= "<option value=$b->id>$b->start_date $b->owner $b->description";
        }
        
        foreach (Doctrine::getTable('Batch')->findAllBatches() as $b) {
            $data->all_batches .= "<option value=$b->id>$b->start_date $b->owner $b->description";
        }
        
        $data->title = 'Quartz Al-Be chemistry';
        $data->subtitle = 'Al-Be extraction from quartz:';
        $data->main = 'quartz_chem/index';
        $this->load->view('template', $data);
    }
    
    /**
     * Form for adding a batch or editing the batch information after it has been added.
     */
    function new_batch()
    {
        $id = $this->input->post('batch_id');
        $is_edit = (bool)$id;
        $data->allow_num_edit = ( ! $is_edit);
        
        if ($is_edit) {
            // it's an existing batch, get it
            $batch = Doctrine::getTable('Batch')->find($id);
            
            if ( ! $batch) {
                show_404('page');
            }
            
            $data->numsamples = $batch->Analysis->count();
        } else {
            // it's a new batch
            $batch = new Batch();
            // number of samples is not known
            $data->numsamples = null;
        }
        
        if ($this->form_validation->run('batches') == false) {
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
            $new_batch = ( ! ((bool)$batch->id));
            $batch->owner = $this->input->post('owner');
            $batch->description = $this->input->post('description');
            if ($new_batch) {
                $batch->start_date = date('Y-m-d');
            }
            $batch->save();
            
            if ($new_batch) {
                // new batch: create the analyses linked to this batch
                $numsamples = $this->input->post('numsamples');
                for ($i = 1; $i <= $numsamples; $i++) {
                    $analysis = new Analysis();
                    $analysis->number_within_batch = $i;
                    $batch->Analysis[] = $analysis;
                }
                $batch->save();
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
        $refresh = (bool)$this->input->post('is_refresh');
        $batch_id = (int)$this->input->post('batch_id');
        
        // grab the batch with carrier data
        $batch = Doctrine::getTable('Batch')->findWithCarriers($batch_id);
        
        if ( ! $batch) {
            die('Batch query failed.');
        }
        
        $num_analyses = $batch->Analysis->count();
        $errors = false;
        
        // if this is a refresh we need to validate the data
        if ($refresh) {
            $is_valid = $this->form_validation->run('load_samples');
            
            // grab the submitted batch data
            $batch->notes = $this->input->post('notes');
            $batch->be_carrier_id = $this->input->post('be_carrier_id');
            $batch->al_carrier_id = $this->input->post('al_carrier_id');
            $batch->wt_be_carrier_init = $this->input->post('wt_be_carrier_init');
            $batch->wt_al_carrier_init = $this->input->post('wt_al_carrier_init');
            $batch->wt_be_carrier_final = $this->input->post('wt_be_carrier_final');
            $batch->wt_al_carrier_final = $this->input->post('wt_al_carrier_final');
            // and array fields for each analysis
            $sample_name = $this->input->post('sample_name');
            $sample_type = $this->input->post('sample_type');
            $diss_bottle_id = $this->input->post('diss_bottle_id');
            $wt_diss_bottle_tare = $this->input->post('wt_diss_bottle_tare');
            $wt_diss_bottle_sample = $this->input->post('wt_diss_bottle_sample');
            $wt_be_carrier = $this->input->post('wt_be_carrier');
            $wt_al_carrier = $this->input->post('wt_al_carrier');

            for ($a = 0; $a < $num_analyses; $a++) { // analysis loop
                $analysis = &$batch->Analysis[$a];
                $analysis->sample_name = $sample_name[$a];
                $analysis->sample_type = $sample_type[$a];
                $analysis->diss_bottle_id = $diss_bottle_id[$a];
                $analysis->wt_diss_bottle_tare = $wt_diss_bottle_tare[$a];
                $analysis->wt_diss_bottle_sample = $wt_diss_bottle_sample[$a];
                $analysis->wt_be_carrier = $wt_be_carrier[$a];
                $analysis->wt_al_carrier = $wt_al_carrier[$a];
            }
            
            if ($is_valid) {
                // valid info, save changes to the database
                $batch->refreshRelated();
                $batch->save();
            } else {
                // validation failed
                $errors = true;
            }
        }
        
        // initialize carrier weights and Alcheck data arrays
        $be_tot_wt = 0;
        $al_tot_wt = 0;
        $diss_bottle_options = array();
        $prechecks = array();
        // get all the dissolution bottle numbers
        $diss_bottles = Doctrine::getTable('DissBottle')->findAll();
        
        for ($a = 0; $a < $num_analyses; $a++) {
            // create diss bottle dropdown options for each sample
            $html = '';
            foreach($diss_bottles as $bottle) {
                $html .= '<option value='.$bottle->id;
                if ($bottle->id == $batch->Analysis[$a]->diss_bottle_id) {
                    $html .= ' selected';
                }
                $html .= "> $bottle->bottle_number";
            }
            $diss_bottle_options[$a] = $html;
            
            $temp_sample_wt = $batch->Analysis[$a]->wt_diss_bottle_sample -
                $batch->Analysis[$a]->wt_diss_bottle_tare;
            
            // get cations while we're at it
            $precheck = Doctrine_Query::create()
                ->from('AlcheckAnalysis a, a.AlcheckBatch b')
                ->select('a.icp_al, a.icp_fe, a.icp_ti, a.wt_bkr_tare, a.wt_bkr_sample, '
                    .'a.wt_bkr_soln, b.prep_date')
                ->where('a.analysis_id = ?', $batch->Analysis[$a]->id)
                ->orderBy('b.prep_date DESC')
                ->limit(1)
                ->fetchOne();
            
            if($precheck AND $batch->AlCarrier) {
                // there's data, calculate the concentrations
                $prechecks[$a]['show'] = true;
                
                if ($precheck['wt_bkr_sample'] == 0 AND $precheck['wt_bkr_tare'] == 0) {
                    $temp_df = 0;
                } else {
                    $temp_df = ($precheck['wt_bkr_soln'] - $precheck['wt_bkr_tare'])
                            / ($precheck['wt_bkr_sample'] - $precheck['wt_bkr_tare']);
                }
                
                $temp_al = $precheck['icp_al'] * $temp_df * $temp_sample_wt / 1000;
                $temp_fe = $precheck['icp_fe'] * $temp_df * $temp_sample_wt / 1000;
                $temp_ti = $precheck['icp_ti'] * $temp_df * $temp_sample_wt / 1000;
                $temp_tot_al = $temp_al + ($batch->Analysis[$a]->wt_al_carrier
                    * $batch->AlCarrier->al_conc) / 1000;
                
                $prechecks[$a]['conc_al'] = sprintf('%.1f', $precheck['icp_al'] * $temp_df);
                $prechecks[$a]['conc_fe'] = sprintf('%.1f', $precheck['icp_fe'] * $temp_df);
                $prechecks[$a]['conc_ti'] = sprintf('%.1f', $precheck['icp_ti'] * $temp_df);
            } else {
                $prechecks[$a]['show'] = false;
                $temp_fe = '--';
                $temp_ti = '--';
                $temp_tot_al = '--';
                
                if ($batch->AlCarrier AND $batch->Analysis[$a]->wt_al_carrier > 0) {
                    $temp_tot_al = $batch->Analysis[$a]->wt_al_carrier
                        * $batch->AlCarrier->al_conc / 1000;
                }
            }
            
            $prechecks[$a]['m_al'] = $temp_tot_al;
            $prechecks[$a]['m_fe'] = $temp_fe;
            $prechecks[$a]['m_ti'] = $temp_ti;
            
            $be_tot_wt += $batch->Analysis[$a]->wt_be_carrier;
            $al_tot_wt += $batch->Analysis[$a]->wt_al_carrier;
        }
        
        // get previous carrier weights
        $data->be_prev = Doctrine::getTable('Batch')
                        ->findPrevBeCarrierWt($batch->id, $batch->be_carrier_id);
        $data->al_prev = Doctrine::getTable('Batch')
                        ->findPrevAlCarrierWt($batch->id, $batch->al_carrier_id);
        
        // create the lists of carrier options
        $data->be_carrier_options = Doctrine::getTable('BeCarrier')
                                    ->getSelectOptions($batch);
        $data->al_carrier_options = Doctrine::getTable('AlCarrier')
                                    ->getSelectOptions($batch);
        
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
    
    /**
     * Produces a sheet to track the progress of samples through the rest of the process.
     */
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

        for ($a = 0; $a < $numsamples; $a++) {
            $precheck = Doctrine_Query::create()
                ->from('AlcheckAnalysis a')
                ->leftJoin('a.AlcheckBatch b')
                ->select('a.sample_name, a.icp_al, a.icp_fe, a.icp_ti, a.wt_bkr_tare, '
                    .'a.wt_bkr_sample, a.wt_bkr_soln, b.prep_date')
                ->where('a.sample_name = ?', $batch->Analysis[$a]->sample_name)
                ->andWhere('a.alcheck_batch_id = b.id')
                ->orderBy('b.prep_date DESC')
                ->limit(1)
                ->fetchOne();

            $tmpa[$a]['tmpSampleWt'] = $batch->Analysis[$a]->wt_diss_bottle_sample
                - $batch->Analysis[$a]->wt_diss_bottle_tare;
            $tmpa[$a]['mlHf'] = round($tmpa[$a]['tmpSampleWt']) * 5 + 5;

            $tmpa[$a]['inAlDb'] = true;
            if ($precheck) {
                $sampleWt = ($precheck['wt_bkr_sample'] - $precheck['wt_bkr_tare']);
                if ($sampleWt != 0) {
                    $temp_df = ($precheck['wt_bkr_soln'] - $precheck['wt_bkr_tare']) / $sampleWt;
                } else {
                    $temp_df = 0;
                }

                $temp_al = $precheck['icp_al'] * $temp_df * $tmpa[$a]['tmpSampleWt'] / 1000;
                $tmpa[$a]['tot_fe'] = sprintf('%.2f',
                    $precheck['icp_fe'] * $temp_df * $tmpa[$a]['tmpSampleWt'] / 1000);
                $tmpa[$a]['tot_ti'] = sprintf('%.2f',
                    $precheck['icp_ti'] * $temp_df * $tmpa[$a]['tmpSampleWt'] / 1000);
                $tmpa[$a]['tot_al'] = sprintf('%.2f', $temp_al
                    + ($batch->Analysis[$a]->wt_al_carrier * $batch->AlCarrier->al_conc) / 1000);
            } elseif ($batch->Analysis[$a]->sample_type === 'BLANK') {
                if ($batch->Analysis[$a]->wt_al_carrier > 0) {
                    $tmpa[$a]['tot_al'] = sprintf('%.2f',
                        ($batch->Analysis[$a]->wt_al_carrier * $batch->AlCarrier->al_conc) / 1000);
                }
                else {
                    $tmpa[$a]['tot_al'] = ' -- ';
                }
                $tmpa[$a]['tot_ti'] = ' -- ';
                $tmpa[$a]['tot_fe'] = ' -- ';
            } else {
                // sample isn't in the Alchecks database
                $tmpa[$a]['inAlDb'] = false;
            }
        }
        
        $data->batch = $batch;
        $data->tmpa = $tmpa;
        $this->load->view('quartz_chem/print_tracking_sheet', $data);
    }

    /**
     * Form to submit solution weights.
     */
    function add_solution_weights() 
    {
        $batch_id = (int)$this->input->post('batch_id');
        $refresh = (bool)$this->input->post('is_refresh');

        $batch = Doctrine_Query::create()
            ->from('Batch b')
            ->leftJoin('b.Analysis a')
            ->leftJoin('a.DissBottle db')
            ->where('b.id = ?', $batch_id)
            ->limit(1)
            ->fetchOne();

        $errors = false;
        if ($refresh) {
            $is_valid = $this->form_validation->run('add_solution_weights');
            
            $batch->merge($this->input->post('batch'));
            $weights = $this->input->post('wt_diss_bottle_total');
            $count = count($weights);
            for ($a = 0; $a < $count; $a++) {
                $batch->Analysis[$a]->wt_diss_bottle_total = $weights[$a];
            }

            if ($is_valid) {
                $batch->save();
            } else {
                $errors = true;
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
        $refresh = (bool)$this->input->post('is_refresh');
        
        $batch = Doctrine_Query::create()
            ->from('Batch b')
            ->leftJoin('b.Analysis a')
            ->leftJoin('a.DissBottle db')
            ->leftJoin('a.Split s')
            ->leftJoin('s.SplitBkr sb')
            ->where('b.id = ?', $batch_id)
            ->limit(1)
            ->fetchOne();
        
        if (! $batch) {
            die('Batch query failed.');
        }
        
        // insert splits and runs if they don't yet exist in the database
        if ($batch->initializeSplitsRuns($batch)) {
            // there were changes, save it
            $batch->save();
        }
        
        $numsamples = $batch->Analysis->count();
        $errors = false;
        if ($refresh) {
            $is_valid = $this->form_validation->run('add_split_weights');
            // change the object data
            $batch->merge($this->input->post('batch'));
            // grab other postdata
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

            if ($is_valid) {
                $batch->save();
            } else {
                $errors = true;
            }
            $batch->refreshRelated();
        }

        $data->errors = $errors;
        $data->numsamples = $numsamples;
        $data->batch = $batch;
        $data->bkr_list = Doctrine::getTable('SplitBkr')->getList();
        $data->title = 'Add split weights';
        $data->main = 'quartz_chem/add_split_weights';
        $this->load->view('template', $data);
    }
    
    /**
     * Form for submitting ICP weighings.
     * @return void
     */
    function add_icp_weights() 
    {
        $batch_id = (int)$this->input->post('batch_id');
        $refresh = (bool)$this->input->post('is_refresh');
        
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
        if ($batch->icp_date === '0000-00-00')  {
            $batch->icp_date = date('Y-m-d');
        }
        
        $errors = false;
        if ($refresh) {
            $is_valid = $this->form_validation->run('add_icp_weights');
            
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
            if ($is_valid) {
                $batch->save();
            } else {
                $errors = true;
            }
        }
        
        $data->errors = $errors;
        $data->numsamples = $numsamples;
        $data->batch = $batch;
        $data->title = 'Add ICP solution weights';
        $data->main = 'quartz_chem/add_icp_weights';
        $this->load->view('template', $data);
    }
    
   /**
    * Form to input ICP results.
    *
    * @return void
    **/
    function add_icp_results() 
    {
        $submit = (bool)$this->input->post('submit');
        $batch_id = (int)$this->input->post('batch_id');
        
        $query = Doctrine_Query::create()
            ->from('Batch b')
            ->where('b.id = ?', $batch_id)
            ->leftJoin('b.Analysis a')
            ->leftJoin('a.Split s')
            ->leftJoin('s.SplitBkr sb')
            ->leftJoin('s.IcpRun r')
            ->orderBy('s.split_num')
            ->addOrderBy('r.run_num');
        
        $batch = $query->fetchOne();
        
        if ($submit == true) {    
            $valid = $this->form_validation->run('add_icp_results');
            $batch->notes = $this->input->post('notes');
            $raw_al = $this->input->post('al_text');
            $raw_be = $this->input->post('be_text');
            $al_lines = split("[\n]", $raw_al);
            $be_lines = split("[\n]", $raw_be);
            
            // now validate our entries
            // this regexp to match a word followed by floating point numbers, just what we want
            $valid_regexp = '/[\w-]+\s+([-+]?[0-9]*\.?[0-9]+)+/i';
            $split_regexp = '/\s+/'; // we'll split on whitespace if it is valid
            $is_al = true;
            foreach (array($al_lines, $be_lines) as $lines) {
                if ($is_al) {
                    $element = 'aluminum';
                    $arr = 'al_arr';
                } else {
                    $element = 'beryllium';
                    $arr = 'be_arr';
                }
                foreach ($lines as $ln) {
                    if (preg_match($valid_regexp, $ln)) {
                        $tmp = preg_split($split_regexp, trim($ln));
                        $key = array_shift($tmp);
                        // make the key of the final array the beaker number, one for be and one for al
                        ${$arr}[$key] = $tmp; 
                    } elseif ($ln !== "") {
                        die("Error in $element input on line: $ln");
                    } 
                }
                $is_al = false;
            }
            
            // check that number of splits is equal
            $len_be = count($be_arr);
            $len_al = count($al_arr);
            if ($len_be != $len_al) {
                die('There must be the same number of splits for aluminum and beryllium.');
            }
            
            // the number of runs submitted must be the same
            $nRunsAl = array();
            $nRunsBe = array();
            foreach ($be_arr as $split) {
                $nRunsBe[] = count($split);
            }
            foreach ($al_arr as $split) {
                $nRunsAl[] = count($split);
            }
            if ($nRunsAl !== $nRunsBe) {
                die('There must be an equal number of ICP runs between Aluminum and Beryllium.');
            }
            
            // make sure our number of splits matches the number created in last page
            $nSplits = 0;
            $nAnalyses = $batch->Analysis->count();
            for ($a = 0; $a < $nAnalyses; $a++) {
                $nSplits += $batch->Analysis[$a]->Split->count();
            }
            if ($len_be != $nSplits) {
                die("The number of splits in the database ($nSplits) does not match the number submitted ($len_be)");
            }
            
            // test that all submitted split beakers exist
            $bkrs = array_unique(array_keys(array_merge($al_arr, $be_arr)));
            $missing = Doctrine::getTable('SplitBkr')->findMissingBkrs($bkrs);
            if (count($missing) != 0) {
                echo "The beakers ";
                // comma separated list, true indicates we want an 'and' before last element
                echo comma_str($missing, true); 
                die(' were not found in the database.<br>'
                    .'Please ensure that you have spelled all '
                    .'their names correctly or add them to the database.');
            }
            
            // data looks good, insert it into the database
            $batch->setIcpResults($al_arr, $be_arr)->save();
        }
        
        // recreate input boxes from database
        list($data->al_text, $data->be_text, $data->nrows) = $batch->generateIcpResultsText();
        
        // in case there were no splits or runs, give it a default number of rows
        if ($data->nrows == 0) {
            $data->nrows = 10;
        }

        $data->batch = $batch;
        $data->title = 'ICP Results Uploading';
        $data->main = 'quartz_chem/add_icp_results';
        $this->load->view('template', $data);
    }
    
   /**
    * Page to select whether to use an ICP result in calculations or not.
    * @return void
    */
    function icp_quality_control()
    {
        // Retrieve intial postdata
        $batch_id = (int)$this->input->post('batch_id');
        $refresh = (bool)$this->input->post('refresh');
        $batch = Doctrine::getTable('Batch')->findCompleteById($batch_id);
        
        // Do a database update if we validate properly
        $errors = false;
        if ($refresh) {
            $valid = $this->form_validation->run('icp_quality_control');
            $batch->notes = $this->input->post('notes');
            $use_be = $this->input->post('use_be');
            $use_al = $this->input->post('use_al');
            $batch->setIcpOKs($use_be, $use_al);
            
            if ($valid) {
                $batch->save();
            } else {
                $errors = true;
            }
        }
        
        $data->batch = $batch->getReportArray(true);
        $data->errors = $errors;
        $data->title = 'ICP Quality Control';
        $data->main = 'quartz_chem/icp_quality_control';
        $this->load->view('template', $data);
    }
    
    // --------
    // REPORTS:
    // --------
    /**
     * Does calculations and prints an intermediate report on the samples.
     * @return void
     */
    function intermediate_report() 
    {
        $batch_id = (int)$this->input->post('batch_id');
        $data->batch = Doctrine::getTable('Batch')->getReportArray($batch_id);
        $data->title = 'Intermediate hard copy of weighings -- Al - Be extraction from quartz';
        $data->todays_date = date('Y-m-d');
        $data->main = 'quartz_chem/intermediate_report';
        $this->load->view('quartz_chem/report_template', $data);
    }
    
    /**
     * Generates the final report for the batch.
     * @return void
     */
    function final_report()
    {
        $batch_id = (int)$this->input->post('batch_id');
        // do all our calculations, pass true to do a complete report
        $data->batch = Doctrine::getTable('Batch')->getReportArray($batch_id, true);
        $data->title = 'Final report -- Al - Be extraction from quartz';
        $data->todays_date = date('Y-m-d');
        $data->main = 'quartz_chem/final_report';
        $this->load->view('quartz_chem/report_template', $data);
    }
    
    // ----------
    // CALLBACKS:
    // ----------
    
    /**
     * @param string $date_string date in YYYY-MM-DD format
     */
    function _valid_date($date_string) 
    {
        if ($this->form_validation->valid_date($date_string)) {
            return true;
        }
        
        $this->form_validation->set_message('valid_date',
            'The %s field must be a valid date in the format YYYY-MM-DD.');
        return false;
    }

}

/* End of file quartz_chem.php */
/* Location: ./system/application/controllers/quartz_chem.php */
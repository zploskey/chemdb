<?=form_open('quartz_chem/icp_quality_control', '', 
    array('batch_id' => $batch->id, 'refresh' => true)) // hidden vars ?>

<table width=800 class=arial10>
	<tr>
		<td> 
		<h3>Batch information:</p></h3>
		Batch ID: <?=$batch->id?> <br>
		Batch start date: <?=$batch->start_date?> <br>
		Batch owner: <?=$batch->owner?> <br>
		Batch description: <?=$batch->description?> <br>
		</td>
	</tr>
	<tr>
		<td colspan=4>
			Batch notes:<br>
			<center>
			<textarea name="notes" rows="5" cols="100"><?=$batch->notes?></textarea>
			</center>
		</td>
	</tr>
	<tr><td><hr></td></tr>
</table>

<table width=800 class=arial8>

<tr><td colspan=11><hr></td></tr>
<tr>
	<td>Analysis ID</td>
	<td>Sample name</td>
	<td>Split number</td>
	<td>Split beaker ID</td>
	<td>Run no.</td>
	<td>ICP [Be]</td>
	<td>ug Be</td>
	<td>OK?</td>
	<td>ICP [Al]</td>
	<td>ug Al</td>
	<td>OK?</td>
</tr>

<? for ($i = 0; $i < $numsamples; $i++): ?>
	
    <tr><td colspan=11><hr></td></tr>
	
    <tr>
    	<td><?=$batch->Analysis[$i]->id?></td>
    	<td><?=$batch->Analysis[$i]->sample_name?></td>
    	
    	<? for ($s = 0; $s > $numsplits; $s++): ?>
    	
    	<td>Split 1</td>
    	<td>{$this_sample['split_bkr_1_ID']}</td>
	
    	// Formerly column 5. Dil factor, split 1
	
    	unset($this_split_1_wt,$this_ICP_1_wt,$this_df_1);
	
    	$this_split_1_wt = $this_sample['wt_split_bkr_1_split'] - $this_sample['wt_split_bkr_1_tare'];
    	$this_ICP_1_wt = $this_sample['wt_split_bkr_1_ICP'] - $this_sample['wt_split_bkr_1_tare'];
    	$this_df_1 = $this_ICP_1_wt / $this_split_1_wt;
	
    	// print "<td>";
    	// printf('%.3f',$this_df_1);
    	// print "</td>";
	
    	// Column 5. Run number
	
    	print "<td>Run 1</td>";
		
    	// Column 6. ICP [Be], bkr 1 run 1
	
    	print "<td>";
    	printf('%.4f',$this_sample['ICP_Be_split1_run1']);
    	print "</td>";
	
    	// Column 7. Total ug Be resulting
	
    	unset($this_tot_Be_b1_r1);
	
    	$this_tot_Be_b1_r1 = $this_df_1 * $this_sample['ICP_Be_split1_run1'] * $this_soln_wt;
	
    	print "<td>";
    	printf('%.1f',$this_tot_Be_b1_r1);
    	print "</td>";
		
    	// Column 8. Use bkr 1 run 1 Be?
	
    	print "<td>";
    	print "<input type=checkbox value=\"y\" name=post_use_Be_b1_r1$i ";
    	if ($this_sample['use_Be_b1_r1'] == 'y') {
    		print "checked";
    	}	
    	print ">";
    	print "</td>";
	
    	// Column 9. ICP [Al], bkr 1 run 1
	
    	print "<td>";
    	printf('%.4f',$this_sample['ICP_Al_split1_run1']);
    	print "</td>";
	
    	// Column 10. Total ug Al resulting
	
    	unset($this_tot_Al_b1_r1);
	
    	$this_tot_Al_b1_r1 = $this_df_1 * $this_sample['ICP_Al_split1_run1'] * $this_soln_wt;
	
    	print "<td>";
    	printf('%.1f',$this_tot_Al_b1_r1);
    	print "</td>";
		
    	// Column 11. Use bkr 1 run 1 Al?
	
    	print "<td>";
    	print "<input type=checkbox value=\"y\" name=post_use_Al_b1_r1$i ";
    	if ($this_sample['use_Al_b1_r1'] == 'y') {
    		print "checked";
    	}	
    	print ">";
    	print "</td>";
	
    	    </tr>
    	<? endfor; ?>
	
    	// ROW 3. SPLIT 1 RUN 2. 
	
    	// Columns 1-4 blank. 
	
    	print "<tr><td colspan=4></td>";
	
    	// Column 5. Run number
	
    	print "<td>Run 2</td>";
		
    	// Column 6. ICP [Be], bkr 1 run 2
	
    	print "<td>";
    	printf('%.4f',$this_sample['ICP_Be_split1_run2']);
    	print "</td>";
	
    	// Column 7. Total ug Be resulting
	
    	unset($this_tot_Be_b1_r2);
	
    	$this_tot_Be_b1_r2 = $this_df_1 * $this_sample['ICP_Be_split1_run2'] * $this_soln_wt;
	
    	print "<td>";
    	printf('%.1f',$this_tot_Be_b1_r2);
    	print "</td>";
		
    	// Column 8. Use bkr 1 run 2 Be?
	
    	print "<td>";
    	print "<input type=checkbox value=\"y\" name=post_use_Be_b1_r2$i ";
    	if ($this_sample['use_Be_b1_r2'] == 'y') {
    		print "checked";
    	}	
    	print ">";
    	print "</td>";
	
    	// Column 9. ICP [Al], bkr 1 run 2
	
    	print "<td>";
    	printf('%.4f',$this_sample['ICP_Al_split1_run2']);
    	print "</td>";
	
    	// Column 10. Total ug Al resulting
	
    	unset($this_tot_Al_b1_r2);
	
    	$this_tot_Al_b1_r2 = $this_df_1 * $this_sample['ICP_Al_split1_run2'] * $this_soln_wt;
	
    	print "<td>";
    	printf('%.1f',$this_tot_Al_b1_r2);
    	print "</td>";
		
    	// Column 11. Use bkr 1 run 2 Al?
	
    	print "<td>";
    	print "<input type=checkbox value=\"y\" name=post_use_Al_b1_r2$i ";
    	if ($this_sample['use_Al_b1_r2'] == 'y') {
    		print "checked";
    	}	
    	print ">";
    	print "</td>";
	
    	// close out the row
	
    	print "</tr>";
	
    	// ROW 4. SPLIT 2 RUN 1. 
	
    	// Columns 1 and 2 blank. 
	
    	print "<tr><td colspan=2></td>";
	
    	// Column 3. Split number
	
    	print "<td>Split 2</td>";
	
    	// Column 4: Split bkr 2 ID
	
    	print "<td>{$this_sample['split_bkr_2_ID']}</td>";
	
    	// Formerly Column 5. Dil factor, split 2
	
    	unset($this_split_2_wt,$this_ICP_2_wt,$this_df_2);
	
    	$this_split_2_wt = $this_sample['wt_split_bkr_2_split'] - $this_sample['wt_split_bkr_2_tare'];
    	$this_ICP_2_wt = $this_sample['wt_split_bkr_2_ICP'] - $this_sample['wt_split_bkr_2_tare'];
    	$this_df_2 = $this_ICP_2_wt / $this_split_2_wt;
	
    	// print "<td>";
    	// printf('%.3f',$this_df_2);
    	// print "</td>";
	
    	// Column 5. Run number
	
    	print "<td>Run 1</td>";
		
    	// Column 6. ICP [Be], bkr 2 run 1
	
    	print "<td>";
    	printf('%.4f',$this_sample['ICP_Be_split2_run1']);
    	print "</td>";
	
    	// Column 7. Total ug Be resulting
	
    	unset($this_tot_Be_b2_r1);
	
    	$this_tot_Be_b2_r1 = $this_df_2 * $this_sample['ICP_Be_split2_run1'] * $this_soln_wt;
	
    	print "<td>";
    	printf('%.1f',$this_tot_Be_b2_r1);
    	print "</td>";
		
    	// Column 8. Use bkr 2 run 1 Be?
	
    	print "<td>";
    	print "<input type=checkbox value=\"y\" name=post_use_Be_b2_r1$i ";
    	if ($this_sample['use_Be_b2_r1'] == 'y') {
    		print "checked";
    	}
    	print ">";
    	print "</td>";
	
    	// Column 9. ICP [Al], bkr 2 run 1
	
    	print "<td>";
    	printf('%.4f',$this_sample['ICP_Al_split2_run1']);
    	print "</td>";
	
    	// Column 10. Total ug Al resulting
	
    	unset($this_tot_Al_b2_r1);
	
    	$this_tot_Al_b2_r1 = $this_df_2 * $this_sample['ICP_Al_split2_run1'] * $this_soln_wt;
	
    	print "<td>";
    	printf('%.1f',$this_tot_Al_b2_r1);
    	print "</td>";
		
    	// Column 11. Use bkr 2 run 1 Al?
	
    	print "<td>";
    	print "<input type=checkbox value=\"y\" name=post_use_Al_b2_r1$i ";
    	if ($this_sample['use_Al_b2_r1'] == 'y') {
    		print "checked";
    	}	
    	print ">";
    	print "</td>";
	
    	// close out the row
	
    	print "</tr>";
	
    	// ROW 5. SPLIT 2 RUN 2
	
    	// Columns 1-4 blank. 
	
    	print "<tr><td colspan=4></td>";
	
    	// Column 5. Run number
	
    	print "<td>Run 2</td>";
		
    	// Column 6. ICP [Be], bkr 2 run 2
	
    	print "<td>";
    	printf('%.4f',$this_sample['ICP_Be_split2_run2']);
    	print "</td>";
	
    	// Column 7. Total ug Be resulting
	
    	unset($this_tot_be_b2_r2);
	
    	$this_tot_Be_b2_r2 = $this_df_2 * $this_sample['ICP_Be_split2_run2'] * $this_soln_wt;
	
    	print "<td>";
    	printf('%.1f',$this_tot_Be_b2_r2);
    	print "</td>";
		
    	// Column 8. Use bkr 2 run 2 Be?
	
    	print "<td>";
    	print "<input type=checkbox value=\"y\" name=post_use_Be_b2_r2$i ";
    	if ($this_sample['use_Be_b2_r2'] == 'y') {
    		print "checked";
    	}	
    	print ">";
    	print "</td>";
	
    	// Column 9. ICP [Al], bkr 2 run 2
	
    	print "<td>";
    	printf('%.4f',$this_sample['ICP_Al_split2_run2']);
    	print "</td>";
	
    	// Column 10. Total ug Al resulting
	
    	unset($this_tot_Al_b2_r2);
	
    	$this_tot_Al_b2_r2 = $this_df_2 * $this_sample['ICP_Al_split2_run2'] * $this_soln_wt;
	
    	print "<td>";
    	printf('%.1f',$this_tot_Al_b2_r2);
    	print "</td>";
		
    	// Column 11. Use bkr 2 run 2 Al?
	
    	print "<td>";
    	print "<input type=checkbox value=\"y\" name=post_use_Al_b2_r2$i ";
    	if ($this_sample['use_Al_b2_r2'] == 'y') {
    		print "checked";
    	}	
    	print ">";
    	print "</td>";
	
    	// close out the row
	
    	print "</tr>";
	
    	// ROW 6. AVERAGES 
	
    	print "<tr>";
	
    	// Columns 1-5 empty
	
    	print "<td colspan=5></td>";
	
    	// Column 6
	
    	print "<td>Average ug:</td>";
	
    	// Columns 7-8. Be average
	
    	// initialize 
	
    	$temp_tot_Be = 0;
    	$temp_sd_Be = 0;
    	$n = 0;
    	unset($this_Be_avg);
    	unset($this_Be_sd);
	
    	// calculate the mean
	
    	if ($this_sample['use_Be_b1_r1'] == 'y') {
    		$temp_tot_Be = $temp_tot_Be + $this_tot_Be_b1_r1;
    		$n = $n+1;
    	}
    	if ($this_sample['use_Be_b1_r2'] == 'y') {
    		$temp_tot_Be = $temp_tot_Be + $this_tot_Be_b1_r2;
    		$n = $n+1;
    	}
    	if ($this_sample['use_Be_b2_r1'] == 'y') {
    		$temp_tot_Be = $temp_tot_Be + $this_tot_Be_b2_r1;
    		$n = $n+1;
    	}
    	if ($this_sample['use_Be_b2_r2'] == 'y') {
    		$temp_tot_Be = $temp_tot_Be + $this_tot_Be_b2_r2;
    		$n = $n+1;
    	}
	
	
    	$this_Be_avg = $temp_tot_Be / $n;
	
    	// calculate the SD
	
    	$n = 0;
	
    	if ($this_sample['use_Be_b1_r1'] == 'y') {
    		$temp_sd_Be = $temp_sd_Be + pow(($this_tot_Be_b1_r1 - $this_Be_avg),2);
    		$n = $n+1;
    	}
    	if ($this_sample['use_Be_b1_r2'] == 'y') {
    		$temp_sd_Be = $temp_sd_Be + pow(($this_tot_Be_b1_r2 - $this_Be_avg),2);
    		$n = $n+1;
    	}
    	if ($this_sample['use_Be_b2_r1'] == 'y') {
    		$temp_sd_Be = $temp_sd_Be + pow(($this_tot_Be_b2_r1 - $this_Be_avg),2);
    		$n = $n+1;
    	}
    	if ($this_sample['use_Be_b2_r2'] == 'y') {
    		$temp_sd_Be = $temp_sd_Be + pow(($this_tot_Be_b2_r2 - $this_Be_avg),2);
    		$n = $n+1;
    	}
	
    	if ($n > 1) {
    		$this_Be_sd = sqrt($temp_sd_Be / ($n-1));
    	}
	
    	print "<td colspan=2>";
    	printf('%.1f',$this_Be_avg);
    	print " +/- ";
    	printf('%.1f',$this_Be_sd);
    	print "</td>";
	
	
    	// Column 9. Nothing
	
    	print "<td></td>";
	
    	// Columns 10-11. Al average. 
	
    	// initialize 
	
    	$temp_tot_Al = 0;
    	$temp_sd_Al = 0;
    	$n = 0;
    	unset($this_Al_avg);
    	unset($this_Al_sd);
	
    	// calculate the mean
	
    	if ($this_sample['use_Al_b1_r1'] == 'y') {
    		$temp_tot_Al = $temp_tot_Al + $this_tot_Al_b1_r1;
    		$n = $n+1;
    	}
    	if ($this_sample['use_Al_b1_r2'] == 'y') {
    		$temp_tot_Al = $temp_tot_Al + $this_tot_Al_b1_r2;
    		$n = $n+1;
    	}
    	if ($this_sample['use_Al_b2_r1'] == 'y') {
    		$temp_tot_Al = $temp_tot_Al + $this_tot_Al_b2_r1;
    		$n = $n+1;
    	}
    	if ($this_sample['use_Al_b2_r2'] == 'y') {
    		$temp_tot_Al = $temp_tot_Al + $this_tot_Al_b2_r2;
    		$n = $n+1;
    	}
	
    	$this_Al_avg = $temp_tot_Al / $n;
	
    	// calculate the SD
	
    	$n = 0;
	
    	if ($this_sample['use_Al_b1_r1'] == 'y') {
    		$temp_sd_Al = $temp_sd_Al + pow(($this_tot_Al_b1_r1 - $this_Al_avg),2);
    		$n = $n+1;
    	}
    	if ($this_sample['use_Al_b1_r2'] == 'y') {
    		$temp_sd_Al = $temp_sd_Al + pow(($this_tot_Al_b1_r2 - $this_Al_avg),2);
    		$n = $n+1;
    	}
    	if ($this_sample['use_Al_b2_r1'] == 'y') {
    		$temp_sd_Al = $temp_sd_Al + pow(($this_tot_Al_b2_r1 - $this_Al_avg),2);
    		$n = $n+1;
    	}
    	if ($this_sample['use_Al_b2_r2'] == 'y') {
    		$temp_sd_Al = $temp_sd_Al + pow(($this_tot_Al_b2_r2 - $this_Al_avg),2);
    		$n = $n+1;
    	}
	
    	if ($n > 1) {
    		$this_Al_sd = sqrt($temp_sd_Al / ($n-1));
    	}
	
    	print "<td colspan=2>";
    	printf('%.1f',$this_Al_avg);
    	print " +/- ";
    	printf('%.1f',$this_Al_sd);
    	print "</td>";
	
    	// close the row
	
    	print "</tr>";
	
    	// ROW 7. PERCENT UNCERTAINTY
	
    	print "<tr>";
	
    	// Colums 1 - 5 empty
	
    	print "<td colspan=5></td>";
	
    	// Column 6
	
    	print "<td>Percent error:</td>";
	
    	// Columns 7-8
	
    	$this_pct_Be_sd = 0;
	
    	if ($this_Be_avg > 0) {
    		$this_pct_Be_sd = 100 * ($this_Be_sd / $this_Be_avg);
    	}
	
    	print "<td colspan=2>";
    	printf('%.1f',$this_pct_Be_sd);
    	print "</td>";
	
    	// Column 9 empty
	
    	print "<td></td>";
	
    	// Columns 10-11 
	
    	$this_pct_Al_sd = 0;
	
    	if ($this_Al_avg > 0) {
    		$this_pct_Al_sd = 100 * ($this_Al_sd / $this_Al_avg);
    	}
	
    	print "<td colspan=2>";
    	printf('%.1f',$this_pct_Al_sd);
    	print "</td>";
	
    	// close the row
	
    	print "</tr>";
	
    	// 	ROW 8. CROSS-CHECKS
	
    	print "<tr>";
	
    	// Colums 1 - 5 empty
	
    	print "<td colspan=5></td>";
	
    	// Column 6
	
    	print "<td>Pct. recovery:</td>";
	
	
    	// Columns 7-8: Be recovery
	
    	// Get Be carrier concentration
	
    	$be_carrier_conc_query = "select Be_conc, del_Be_conc from be_carriers where Be_carrier_ID='{$batch_array['Be_carrier_ID']}'";
    	$be_carrier_conc_result = mysql_query($be_carrier_conc_query);
    	$be_carrier_conc_array = mysql_fetch_array($be_carrier_conc_result);
	
    	unset($this_Be_oughta,$this_Be_recovery);
	
    	$this_Be_oughta = $be_carrier_conc_array[0] * $this_sample['wt_Be_carrier'];
	
    	$this_Be_recovery = 100 * $this_Be_avg / $this_Be_oughta;
	
    	print "<td colspan=2>";
    	printf('%.1f',$this_Be_recovery);
    	print "</td>";
	
    	// Column 9 empty
	
    	print "<td></td>";
	
    	// Columns 10-11: Al recovery
	
    	// Get Al carrier concentration
	
    	$al_carrier_conc_query = "select Al_conc, del_Al_conc from al_carriers where Al_carrier_ID='{$batch_array['Al_carrier_ID']}'";
    	$al_carrier_conc_result = mysql_query($al_carrier_conc_query);
    	$al_carrier_conc_array = mysql_fetch_array($al_carrier_conc_result);
	
    	// Get Al from sample
	
    	// Attempt to obtain Al concentration from Al checks database. 
	
    	unset($temp_df,$temp_sample_wt,$temp_Al,$temp_tot_Al,$this_Al_recovery);
	
    	// Switch databases
    	$db_selected = mysql_select_db("alchecks");	

    	$get_cations_query = "select sample_name,
    			ICP_Al,
    			wt_bkr_tare,wt_bkr_sample,wt_bkr_soln,
    			prep_date 
    			from analyses,batches 
    			where sample_name = \"{$this_sample['sample_name']}\" 
    			and analyses.batch_ID = batches.batch_ID 
    			order by prep_date desc limit 1";			
			
    	$get_cations_result = mysql_query($get_cations_query) 
    		or die("Get cations query failed -- sample $i --" . mysql_error() . " - $db_selected -");

    	if (mysql_num_rows($get_cations_result) > 0) {
    		$precheck = mysql_fetch_array($get_cations_result,MYSQL_ASSOC);
    		$temp_df = ($precheck['wt_bkr_soln'] - $precheck['wt_bkr_tare']) / ($precheck['wt_bkr_sample'] - $precheck['wt_bkr_tare']);
    		$temp_sample_wt = $this_sample['wt_diss_bottle_sample'] - $this_sample['wt_diss_bottle_tare'];
    		$temp_Al = $precheck['ICP_Al'] * $temp_df * $temp_sample_wt;
    		$temp_tot_Al = $temp_Al + ($this_sample['wt_Al_carrier'] * $al_carrier_conc_array[0]);
		
    		$this_Al_recovery = 100 * $this_Al_avg / $temp_tot_Al;
    		print "<td colspan=2>";
    		printf('%.1f',$this_Al_recovery);
    		print "</td>";
    	}
    	else {
    		if ($this_sample['wt_Al_carrier'] > 0) {
    			$temp_tot_Al = ($this_sample['wt_Al_carrier'] * $al_carrier_conc_array[0]);
    			$this_Al_recovery = 100 * $this_Al_avg / $temp_tot_Al;
		
    			print "<td colspan=2>";
    			printf('%.1f',$this_Al_recovery);
    			print "</td>";
    		}
    		else {
    			print "<td colspan=2>--</td>";
    		}
    	}
	
	
    	// Switch databases back 
    	mysql_select_db("al_be_quartz_chem");
	
	
    	// close the row
	
    	print "</tr>";
	
    	// print a save and refresh button every two rows
	
    	if ( ($i % 2) == 0) {
    		print "<tr><td colspan=11><hr></td></tr>
    			<tr>
    			<td colspan=11 align=center>
    			<input type=submit value=\"Save and refresh\">
    			</td>
    			</tr>";
    	}
	
<? endfor; ?>

<tr><td colspan=11><hr></td></tr>

</table>

<?=form_close()?>

<?=form_open('quartz_chem/index')?>
    <div align="center">
        <input type="submit" value="I'm done -- back to main menu">
    </div>	
<?=form_close()?>

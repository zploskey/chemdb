<?php echo form_open(site_url('quartz_chem/add_icp_results));?>

// --------------- CASE-DEPENDENT STUFF ------------

// TWO CASES FOR THIS FORM. 
// CASE ONE, NOT A REFRESH. 
// 	  is_refresh IS NOT SET 
//    GET THE DATA FROM THE DATABASE
//	  DISPLAY THE FORM WITH THE DATA
// CASE 2, REFRESH OR EDIT EXISTING BATCH. 
//    is_refresh IS SET
//	  UPDATE THE POSTED VARIABLES INTO THE DATABASE
//	  GET THEM BACK FROM THE DATABASE
//	  DISPLAY THE FORM WITH THE DATA

// this_batch_id_html is always set. 

// One conditional only. If it's a refresh, update. 

if (isset($_POST['is_refresh'])) {

	// do the update
	
	// Things that have to be updated are:
	// Each sample  -- 8 ICP measurements 
	// ICP_Al_split1_run1
	// ICP_Al_split1_run2
	// ICP_Al_split2_run1
	// ICP_Al_split2_run2
	// ICP_Be_split1_run1
	// ICP_Be_split1_run2
	// ICP_Be_split2_run1
	// ICP_Be_split2_run2
	// and 8 slots for whether or not to use the ICP measurements. 
	// use_Be_b1_r1
	// use_Be_b1_r2
	// use_Be_b2_r1
	// use_Be_b2_r2
	// use_Al_b1_r1
	// use_Al_b1_r2
	// use_Al_b2_r1
	// use_Al_b2_r2
	
	// also batch_notes
	
	// do the batch update
	
	$batch_update_query = "update batches
	set batch_notes = \"{$_POST['post_batch_notes']}\"
	where batch_ID=$this_batch_id";
	
	$batch_update_result = mysql_query($batch_update_query) or die('Batch update query failed');
	
	// do the sample update
		
	// The data come in as two text strings, one for Al and one for Be
	// with the form:  bkr1run1 bkr1run2 bkr2run1 bkr2run2
	// so we have to parse the input string and update the database. 
	
	for ($j=1; $j <= $numsamples; $j++) {
	
		// get the text strings 
	
		eval('$this_Al_string = $_POST[\'post_Al_string' ."$j" .'\'];');		
		eval('$this_Be_string = $_POST[\'post_Be_string' ."$j" .'\'];');	
		
		// parse the text strings
		
		$Be_bits = preg_split("/[\s]/",$this_Be_string,-1,PREG_SPLIT_NO_EMPTY);
		
		$Al_bits = preg_split("/[\s]/",$this_Al_string,-1,PREG_SPLIT_NO_EMPTY);
				
		// update the ICP measurements
		
		$sample_update_query = "update analyses 
			set ICP_Al_split1_run1=\"$Al_bits[0]\",
			ICP_Al_split1_run2=\"$Al_bits[1]\",
			ICP_Al_split2_run1=\"$Al_bits[2]\",
		  	ICP_Al_split2_run2=\"$Al_bits[3]\",
		  	ICP_Be_split1_run1=\"$Be_bits[0]\",
			ICP_Be_split1_run2=\"$Be_bits[1]\",
			ICP_Be_split2_run1=\"$Be_bits[2]\",
		  	ICP_Be_split2_run2=\"$Be_bits[3]\"
			where batch_ID=\"$this_batch_id\"
			and number_within_batch=$j";
			
		$sample_update_result = mysql_query($sample_update_query);

		if (!$sample_update_result) {
			$error_text = mysql_error(). ":" . mysql_error_num();
			print "ICP data update failed -- sample $j ***";
			print $error_text;
			print "***";
		}
		
		unset($this_Al_string,$this_Be_string,$Al_bits,$Be_bits,$Al_temp,$Be_temp);
		
		// Now extract the checkbox flags
		
		eval('$this_use_Be_b1_r1 = $_POST[\'post_use_Be_b1_r1' ."$j" .'\'];');
		if ($this_use_Be_b1_r1 != 'y') {$this_use_Be_b1_r1 = 'n';}
		
		eval('$this_use_Be_b1_r2 = $_POST[\'post_use_Be_b1_r2' ."$j" .'\'];');
		if ($this_use_Be_b1_r2 != 'y') {$this_use_Be_b1_r2 = 'n';}
		
		eval('$this_use_Be_b2_r1 = $_POST[\'post_use_Be_b2_r1' ."$j" .'\'];');
		if ($this_use_Be_b2_r1 != 'y') {$this_use_Be_b2_r1 = 'n';}
		
		eval('$this_use_Be_b2_r2 = $_POST[\'post_use_Be_b2_r2' ."$j" .'\'];');
		if ($this_use_Be_b2_r2 != 'y') {$this_use_Be_b2_r2 = 'n';}
		
		eval('$this_use_Al_b1_r1 = $_POST[\'post_use_Al_b1_r1' ."$j" .'\'];');
		if ($this_use_Al_b1_r1 != 'y') {$this_use_Al_b1_r1 = 'n';}
		
		eval('$this_use_Al_b1_r2 = $_POST[\'post_use_Al_b1_r2' ."$j" .'\'];');
		if ($this_use_Al_b1_r2 != 'y') {$this_use_Al_b1_r2 = 'n';}
		
		eval('$this_use_Al_b2_r1 = $_POST[\'post_use_Al_b2_r1' ."$j" .'\'];');
		if ($this_use_Al_b2_r1 != 'y') {$this_use_Al_b2_r1 = 'n';}
		
		eval('$this_use_Al_b2_r2 = $_POST[\'post_use_Al_b2_r2' ."$j" .'\'];');
		if ($this_use_Al_b2_r2 != 'y') {$this_use_Al_b2_r2 = 'n';}
		
		// Update the checkbox flags
		
		$checkbox_update_query = "update analyses
			set use_Be_b1_r1 = \"$this_use_Be_b1_r1\",
			use_Be_b1_r2 = \"$this_use_Be_b1_r2\",
			use_Be_b2_r1 = \"$this_use_Be_b2_r1\",
			use_Be_b2_r2 = \"$this_use_Be_b2_r2\",
			use_Al_b1_r1 = \"$this_use_Al_b1_r1\",
			use_Al_b1_r2 = \"$this_use_Al_b1_r2\",
			use_Al_b2_r1 = \"$this_use_Al_b2_r1\",
			use_Al_b2_r2 = \"$this_use_Al_b2_r2\"
			where batch_ID=\"$this_batch_id\"
			and number_within_batch=$j";
		
		$checkbox_update_result = mysql_query($checkbox_update_query);
		
		// deals with weird lost-connection message -- this is still mysterious. 
	
		if (!$checkbox_update_result) {
			print "<small>Checkbox update failed -- sample $j --";
				print mysql_error();
			print ":";
			print mysql_errno();
			print "<br>  -- trying again -- <br></small>";
			$checkbox_update_result = mysql_query($checkbox_update_query);
			if (!$checkbox_update_query) {
				print "Failed twice. Bailing.";
				exit();
			}	
		}
	}

}


// --------------- ALL CASES - GET DATA AND PRINT FORM ------------


// print the header


print <<<_BLOCK

<table width=800>
	<tr><td colspan=2><hr></td></tr>
	<tr><td class=arial14>
		<i><h2>Add ICP results</h2><i>
		</td>
		<td align=right><img src="../img/logo.jpeg">
		</td>
	</tr>
	<tr><td colspan=2><hr></td></tr>
	<tr><td colspan=2 class=arial12>
		<b><i>You are logged in as: 
		{$_SERVER['REMOTE_USER']}
		</b></i></td>
	</tr>
	<tr><td colspan=2><hr></p></td></tr>
</table>

_BLOCK;

// set hidden variables
// set this_batch_id_html
// set is_refresh

print <<<_BLOCK

<input type=hidden name=this_batch_id_html value=$this_batch_id>
<input type=hidden name=is_refresh value="yes">

_BLOCK;

// get the batch information

$batch_recovery_query = "select * from batches where batch_ID = $this_batch_id";
$batch_recovery_result = mysql_query($batch_recovery_query) or die('Batch recovery query failed.');	
$batch_array = mysql_fetch_array($batch_recovery_result,MYSQL_ASSOC);

// print the batch part of the form
	// show batch ID, start date, owner
	
print <<<_BLOCK

<table width=800 class=arial10>
	<tr>
		<td> 
		<h3>Batch information:</p></h3>
		Batch ID: $this_batch_id <br>
		Batch start date: {$batch_array['start_date']} <br>
		Batch owner: {$batch_array['batch_owner']} <br>
		Batch description: {$batch_array['batch_desc']} </p>
		</td>
	</tr>
	<tr>
		<td colspan=4>
			Batch notes:<br><center>
			<textarea name=post_batch_notes rows=5 cols=100>{$batch_array['batch_notes']}</textarea></center>
		</td>
	</tr>
	<tr><td><hr></td></tr>
</table>

_BLOCK;

// open the sample table

print <<<_BLOCK

<table width=800 class=arial8>

<tr>
	<td colspan=11 class=arial12>Sample information:</p></td>
</tr>
<tr><td colspan=11><hr></td></tr>
<tr><td colspan=11 class=arial10>
	<blockquote>Data entry instructions: enter text strings containing four Be or Al ICP measurements in the following order:<br>
	<pre>
	bkr1_run1 bkr1_run2 bkr2_run1 bkr2_run2
	</pre>
	This is most easily accomplished by cutting-and-pasting from Excel.
	Remember to do the machine drift corrections first, and paste in the corrected measurements. 
	</blockquote>
	</td>
</tr>
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

_BLOCK;

// Begin sample loop

for ($i = 1; $i <= $numsamples; $i++) {

	// get the sample row
	
	$this_sample_query = "select * from analyses where batch_ID = $this_batch_id and number_within_batch = $i";
	$this_sample_result = mysql_query($this_sample_query) or die('Sample query failed');
	$this_sample = mysql_fetch_array($this_sample_result,MYSQL_ASSOC);
	
	unset($this_soln_wt);
	
	$this_soln_wt = $this_sample['wt_diss_bottle_total'] - $this_sample['wt_diss_bottle_tare'];
	
	// print a line
	
	print "<tr><td colspan=11><hr></td></tr>";
	
	// ROW 1. DATA ENTRY
	
	print "<tr>";
	
	// Columns 1-4 - Be
	
	// assemble string
	
	$this_Be_string = $this_sample['ICP_Be_split1_run1'] . " " .
		$this_sample['ICP_Be_split1_run2'] . " " .
		$this_sample['ICP_Be_split2_run1'] . " " .
		$this_sample['ICP_Be_split2_run2'];
	
	print "<td colspan=4>";
	print "[Be] measurements:<input type=text
		size=36
		name=post_Be_string$i value=\"$this_Be_string\">";
	print "</td>";
	
	// Columns 5-11 - Al
	
	$this_Al_string = $this_sample['ICP_Al_split1_run1'] . " " . 
		$this_sample['ICP_Al_split1_run2'] . " " . 
		$this_sample['ICP_Al_split2_run1'] . " " . 
		$this_sample['ICP_Al_split2_run2'];
	
	print "<td colspan=7>";
	print "[Al] measurements: <input type=text
		size=36
		name=post_Al_string$i value=\"$this_Al_string\">";
	print "</td>";
	
	// close row
	
	print "</tr>";
	
	// ROW 2. SPLIT 1 RUN 1.
	
	print "<tr>";
	
	// Column 1. Show analysis id
	
	print "<td> {$this_sample['analysis_ID']} </td>";
	
	// Column 2. Sample name
	
	print "<td>{$this_sample['sample_name']}</td>";
	
	// Column 3. Split number
	
	print "<td>Split 1</td>";
	
	// Column 4: Split bkr 1 ID
	
	print "<td>{$this_sample['split_bkr_1_ID']}</td>";
	
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
	
	// close out the row
	
	print "</tr>";
	
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
	
	

}

// close out the table


print "<tr><td colspan=11><hr></td></tr></table>";
		
// close the form

print "</form>";

print "<table width=800>";

// new form, print back-to-options button

print "<form method=\"post\" action=\"quartz_chem_options.php\">";

print "<tr>
	<td align=center>
	<input type=submit value=\"I'm done -- back to main menu\">
	</td>
	</tr>";
	
print "<tr><td><hr></td></tr></table>";	
	
// close out the form

<?=form_close()?>
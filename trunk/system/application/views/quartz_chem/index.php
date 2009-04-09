<table>
	<!--- CREATE A NEW BATCH -->
	<!-- action=start_new_quartz_prep.php -->
	<?=form_open('quartz_chem/new_batch')?>
		<tr>
			<td><b><i>Create a new batch</i></b></td>
			<td align=center>
				<input type=submit width=40 value="Create a new batch">
			</td>
		</tr>
	<?=form_close()?>

	<tr><td colspan=2><hr></td></tr>
	<p/>
	<!--- SAMPLE LOADING AND CARRIER ADDITION -->

	<tr>
		<td colspan=2><i><b>Sample loading and carrier addition</b></i></td>
	</tr>

	<!-- action=quartz_sample_loading.php -->
	<?=form_open(site_url('quartz_chem/load_samples'))?>
		<tr>
			<td align=center>
				<i>Batch:</i>
				<select name="batch_id">
					<?=$open_batches?>
				</select>

			</td>
			<td align=center>
				<input type="submit" value="Sample loading and carrier addition for this batch">
			</td>
		</tr>
	<?=form_close()?>

	<tr><td colspan=2><hr/></td></tr>
	<p/>

	<!--- PRINT THE TRACKING SHEET -->

	<tr>
		<td colspan=2><i><b>Print lab tracking sheet</b></i></td>
	</tr>
	<!-- action=print_tracking_sheet.php -->
	<?=form_open('quartz_chem/print_tracking_sheet')?>
		<tr>
			<td align=center>
				<i>Batch:</i>
				<select name="batch_id">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="Print tracking sheet for this batch">
			</td>
		</tr>
	<?=form_close()?>
	<tr><td colspan=2><hr></td></tr>
	<p/>

	<!--- ADD TOTAL SOLUTION WEIGHTS -->

	<tr>
		<td colspan=2><i><b>Add total solution weights</i></b></td>
	</tr>
	<!-- action=add_total_weights.php -->
	<?=form_open('quartz_chem/add_total_weights')?>
		<tr>
			<td align=center>
				<i>Batch:</i>
				<select name="batch_id">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="Add total solution weights for this batch">
			</td>
		</tr>
	<?=form_close()?>
	<tr><td colspan=2><hr></td></tr>
	<p/>

	<!--- ADD SPLIT WEIGHTS -->

	<tr>
		<td colspan=2 class=xl25>
			<b><i>Add split weights</i></b>
		</td>
	</tr>
	<!-- action=add_split_weights.php -->
	<?=form_open('quartz_chem/add_split_weights')?>
		<tr>
			<td align=center>
				<i>Batch:</i>   
				<select name="batch_id">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="Add split weights for this batch">
			</td>
		</tr>
	<?=form_close()?>
	<tr><td colspan=2><hr></td></tr><p/>


	<!--- ADD ICP WEIGHTS -->

	<tr>
		<td colspan=2 class=xl25>
			<i><b>Add ICP solution weights</b></i>
		</td>
	</tr>
	<!-- action=add_ICP_weights.php -->
	<?=form_open('quartz_chem/add_ICP_weights')?>
		<tr>
			<td align=center>
				<i>Batch:</i>   
				<select name="batch_id">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="Add ICP solution weights for this batch">
			</td>
		</tr>
	<?=form_close()?>

	<tr><td colspan=2><hr></td></tr><p/>

	<!--- ADD ICP RESULTS -->

	<tr>
		<td colspan=2 class=xl25>
			<i><b>Enter ICP results</b></i>
		</td>
	</tr>
	<!-- action=add_ICP_results.php -->
	<?=form_open('quartz_chem/add_ICP_weights')?>	
		<tr>
			<td align=center>
				<i>Batch:</i>   
				<select name="batch_id">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="Add ICP results for this batch">
			</td>
		</tr>
	<?=form_close()?>
	<tr><td colspan=2><hr></p></td></tr>

	<!--- FINAL REPORT -->

	<tr>
		<td colspan=2 class=xl25>
			<i><b>Final report</i></b>
		</td>
	</tr>
	<!-- action=final_report.php -->
	<?=form_open('quartz_chem/final_report')?>
		<tr>
			<td align=center>
				<i>Batch:</i>
				<select name="batch_id">
					<?=$all_batches?>
				</select>

			</td>
			<td align=center>
				<input type=submit value="Get final report for this batch">
			</td>
		</tr>
	<?=form_close()?>
	<tr><td colspan=2 ><hr></td></tr>

	<tr>
		<td colspan=2>
			<i><b>Take a completed batch off the active list</b></i>
		</td>
	</tr>
	<!-- action="quartz_chem_options.php" -->
	<?=form_open('quartz_chem/index')?>
		<input type="hidden" name="is_lock" value="yes">
		<tr>
			<td align=center>
				<i>Batch:</i>   
				<select name="batch_id">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="This batch is done!">
			</td>
		</tr>
	<?=form_close()?>

	<tr><td colspan=2 ><hr></td></tr>
</table>
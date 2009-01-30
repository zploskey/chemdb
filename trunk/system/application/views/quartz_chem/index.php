<table>
	<!--- CREATE A NEW BATCH -->
	<!-- action=start_new_quartz_prep.php -->
	<?=form_open('quartz_chem/new')?>
		<tr>
			<td><i><b>Create a new batch</td></i><b>
			<td align=center>
				<input type=submit width=40 value="Create a new batch">
			</td>
		</tr>
	</form>

	<tr><td colspan=2><hr></p></td></tr>

	<!--- SAMPLE LOADING AND CARRIER ADDITION -->

	<tr>
		<td colspan=2><i><b>Sample loading and carrier addition</i></b></td>
	</tr>

	<!-- action=quartz_sample_loading.php -->
	<?=form_open('quartz_chem/sample_loading')?>
		<tr>
			<td align=center>
				<i>Batch:</i>
				<select name="this_batch_id_html">
					<?=$open_batches?>
				</select>

			</td>
			<td align=center>
				<input type="submit" value="Sample loading and carrier addition for this batch">
			</td>
		</tr>
	</form>
	<tr><td colspan=2><hr></p></td></tr>

	<!--- PRINT THE TRACKING SHEET -->

	<tr>
		<td colspan=2><i><b>Print lab tracking sheet</i></b></td>
	</tr>
	<!-- action=print_tracking_sheet.php -->
	<?=form_open('quartz_chem/print_tracking_sheet')?>
		<tr>
			<td align=center>
				<i>Batch:</i>
				<select name="this_batch_id_html">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="Print tracking sheet for this batch">
			</td>
		</tr>
	</form>
	<tr><td colspan=2><hr></p></td></tr>

	<!--- ADD TOTAL SOLUTION WEIGHTS -->

	<tr>
		<td colspan=2><i><b>Add total solution weights</i></b></td>
	</tr>
	<!-- action=add_total_weights.php -->
	<?=form_open('quartz_chem/add_total_weights')?>
		<tr>
			<td align=center>
				<i>Batch:</i>
				<select name="this_batch_id_html">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="Add total solution weights for this batch">
			</td>
		</tr>
	</form>
	<tr><td colspan=2><hr></p></td></tr>

	<!--- ADD SPLIT WEIGHTS -->

	<tr>
		<td colspan=2 class=xl25>
			<i><b>Add split weights</i></b>
		</td>
	</tr>
	<!-- action=add_split_weights.php -->
	<?=form_open('quartz_chem/add_split_weights')?>
		<tr>
			<td align=center>
				<i>Batch:</i>   
				<select name="this_batch_id_html">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="Add split weights for this batch">
			</td>
		</tr>
	</form>
	<tr><td colspan=2><hr></p></td></tr>

	<!--- ADD ICP WEIGHTS -->

	<tr>
		<td colspan=2 class=xl25>
			<i><b>Add ICP solution weights</i></b>
		</td>
	</tr>
	<!-- action=add_ICP_weights.php -->
	<?=form_open('quartz_chem/add_ICP_weights')?>
		<tr>
			<td align=center>
				<i>Batch:</i>   
				<select name="this_batch_id_html">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="Add ICP solution weights for this batch">
			</td>
		</tr>
	</form>
	<tr><td colspan=2><hr></p></td></tr>

	<!--- ADD ICP RESULTS -->

	<tr>
		<td colspan=2 class=xl25>
			<i><b>Enter ICP results</i></b>
		</td>
	</tr>
	<!-- action=add_ICP_results.php -->
	<?=form_open('quartz_chem/add_ICP_weights')?>	
		<tr>
			<td align=center>
				<i>Batch:</i>   
				<select name="this_batch_id_html">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="Add ICP results for this batch">
			</td>
		</tr>
	</form>
	<tr><td colspan=2><hr></p></td></tr>

	<!--- FINAL REPORT -->

	<tr>
		<td colspan=2 class=xl25>
			<i><b>Final report</i></b>
		</td>
	</tr>
	<?=form_open('quartz_chem/final_report')?>
	<!-- action=final_report.php -->
		<tr>
			<td align=center>
				<i>Batch:</i>
				<select name="this_batch_id_html">
					<?=$all_batches?>
				</select>

			</td>
			<td align=center>
				<input type=submit value="Get final report for this batch">
			</td>
		</tr>
	</form>
	<tr><td colspan=2 ><hr></td></tr>

	<tr>
		<td colspan=2>
			<i><b>Take a completed batch off the active list</i></b>
		</td>
	</tr>
	<!-- action="quartz_chem_options.php" -->
	<?=form_open('quartz_chem/index')?>
		<input type="hidden" name="is_lock" value="yes">
		<tr>
			<td align=center>
				<i>Batch:</i>   
				<select name="this_batch_id_html">
					<?=$open_batches?>
				</select>
			</td>
			<td align=center>
				<input type=submit value="This batch is done!">
			</td>
		</tr>
	</form>
	<tr><td colspan=2 ><hr></td></tr>
</table>
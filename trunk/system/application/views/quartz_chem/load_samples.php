<?=form_open(site_url('quartz_chem/load_samples'))?>
	<input type="hidden" name="batch_id" value="<?=$batch->id?>">
	<input type="hidden" name="is_refresh" value="TRUE">
	
	<table width=800 class=arial10>
		<tr>
			<td colspan=4 width=400> 
			<h3>Batch information:</h3><p/>
			Batch ID: <?=$batch->id?><br>
			Batch start date: <?=$batch->start_date?> <br>
			Batch owner: <?=$batch->owner?> <br>
			Batch description: <?=$batch->description?> <p/>
			</td>
		</tr>
		<tr>
			<td colspan=4>
				Batch notes:<br>
				<center>
					<textarea name="batch[notes]" rows=5 cols=100><?=$batch->notes?></textarea>
				</center>
			</td>
		</tr>
		<tr>
			<td>
				Be carrier: 
			</td>
			<td>
				<select name="batch[be_carrier_id]">
					<?=$be_carrier_options?>
				</select>
			</td>
			<td> Al carrier:</td>
			<td>
				<select name="batch[al_carrier_id]">
					<?=$al_carrier_options?>
				</select>
			</td>
		</tr>
		<tr><td></td>
			<td>
				[Be]: <?=$batch->BeCarrier->be_conc?> +/- <?=$batch->BeCarrier->del_be_conc?>
				ug/g
			</td><td></td>
			<td>
				[Al]: <?=$batch->AlCarrier->al_conc?> +/- <?=$batch->AlCarrier->del_al_conc?>
				ug/g
			</td>
		</tr>
		<tr>
			<td>Be carrier previous wt:</td>
			<td><?=$be_prev->wt_be_carrier_final?> ( <?=$be_prev->start_date?> )</td>

			<td>Al carrier previous wt:</td>
			<td><?=$al_prev->wt_al_carrier_final?> ( <?=$al_prev->start_date?> )</td>
		</tr>
		<tr>
			<td>
				Be carrier initial wt:
			</td>
			<td>
				<input type=text name="batch[wt_be_carrier_init]" width=10 value="<?=$batch->wt_be_carrier_init?>">
			</td>
			<td> 
				Al carrier initial wt:
			</td>
			<td>
				<input type=text name="batch[wt_al_carrier_init]" width=10 value="<?=$batch->wt_al_carrier_init?>">
			</td>
		</tr>
		<tr>
			<td>
				Be carrier final wt:
			</td>
			<td>
				<input type=text name="batch[wt_be_carrier_final]" width=10 value="<?=$batch->wt_be_carrier_final?>">
			</td>
			<td> 
				Al carrier final wt:
			</td>
			<td>
				<input type=text name="batch[wt_al_carrier_final]" width=10 value="<?=$batch->wt_al_carrier_final?>">
			</td>
		</tr>
		<tr><td colspan=4><hr></td></tr>
	</table>

	<table width=800 class=arial8>
		<tr>
			<td colspan=3 class=arial12>Sample information:</td>
			<td colspan=7 class=arial10>
				<i>Open new window to create Al/Fe/Ti concentrations for samples not in database:
				<!-- TODO: dummy alcheck window -->
				<a href="dummy_al_check.php" target="dummy_alcheck_window">click here</a></i>
			</td>
		</tr>
		<tr>
			<td>Analysis ID</td>
			<td>Sample name</td>
			<td>Type</td>
			<td>Diss bottle ID</td>
			<td>Wt. bottle<br> tare</td>
			<td>Wt. bottle <br>and sample</td>
			<td>Wt. sample</td>
			<td>Wt. Be <br> carrier sol'n</td>
			<td>Mass Be </td>
			<td>Wt. Al <br> carrier sol'n</td>
		</tr>

		<!-- Display all the analysis information -->
		<?php $i = 0; ?>
		<?php foreach ($batch->Analysis as $a): // main display loop ?>
			<tr><td colspan=10><hr></td></tr>
			<tr>
				<td> <?=$a->id?> </td>
				<td>
					<input type=text size=16 name="batch[Analysis][][name]" value="<?=$a->sample_name?>">
				</td>
				<!-- Sample type dropdown -->
				<td>
					<select name="batch[Analysis][][sample_type]">
				
						<?php if ($a->sample_type == "BLANK"): ?>
							<option value="SAMPLE">Sample</option>
							<option value="BLANK" selected>Blank</option>
						<?php else: ?>
							<option value="SAMPLE" selected>Sample</option>
							<option value="BLANK">Blank</option>
						<?php endif; ?>

					</select>
				</td>
			
				<!-- Bottle id dropdown -->
				<td>
					<select name="batch[Analysis][][diss_bottle_id]">
						<?=$diss_bottle_options[$i]?>
					</select>
				</td>
			
				<!-- bottle tare wt -->
				<td>
					<input type=text size=8 name="batch[Analysis][][wt_diss_bottle_tare]" value="<?=$a->wt_diss_bottle_tare?>">
				</td>

				<!-- bottle + sample weight -->
				<td>
					<input type=text size=8 name=batch[Analysis][][wt_diss_bottle_sample] value="<?=$a->wt_diss_bottle_sample?>">
				</td>

				<!-- Column 7. Sample weight -->
				<td>
					<?=sprintf('%.4f', ($a->wt_diss_bottle_sample - $a->wt_diss_bottle_tare))?>
				</td>

				<!-- Column 8. Be carrier wt -->
				<td>
					<input type=text size=8 name="wt_be_carrier[]" value="<?=$a->wt_be_carrier?>">
				</td>

				<!-- Column 9. Be mass -->
				<td>
					<?=sprintf('%.1f', ($a->wt_be_carrier * $batch->BeCarrier->be_conc))?>
				</td>

				<!-- Column 10. Al carrier wt -->
				<td>
					<input type=text size=8 name="wt_al_carrier[]" value="<?=$a->wt_al_carrier?>">
				</td>

			</tr><tr><td colspan=10></td></tr><p/>
			<tr>
				<td colspan=4>
					<?php if ($prechecks[$i]['show']): ?>
						Concentrations in quartz: 
						[Al] = <?php printf('%.1f', $prechecks[$i]['conc_al']); ?>
						[Fe] = <?php printf('%.1f', $prechecks[$i]['conc_fe']); ?>
						[Ti] = <?php printf('%.1f', $prechecks[$i]['conc_ti']); ?>
					<?php else: ?>
						Sample name not in Al checks database
					<?php endif; ?>
				</td>
			<!--	</td> -->

				<td>Total Al (mg): <br>(incl. carrier)</td>
				<td><?php printf('%.2f', $prechecks[$i]['m_al']); ?></td>
				<td>Total Fe (mg):</td>
				<td><?php printf('%.2f', $prechecks[$i]['m_fe']); ?></td>
				<td>Total Ti (mg):</td>
				<td><?php printf('%.2f', $prechecks[$i]['m_ti']); ?></td>
			</tr>
		
			<!-- Print save and refresh button every two rows -->
			<?php if ( ($i % 2) != 0): ?>
				<tr>
					<td colspan=11><hr></td>
				</tr>
				<tr>
					<td colspan=11 align=center>
						<input type=submit value="Save and refresh">
					</td>
				</tr>
			<?php endif; ?>
			<?php $i++; ?>
		<?php endforeach; // main display loop ?>
			
		<tr><td colspan=10><hr></td></tr>
	</table>
		
	<table width=800>
		<tr><td class=arial10>Carrier weight comparison:</td></tr><p/>
		<tr class=arial10>
			<td>Be carrier: </td>
			<td> Final less initial wt:</td><td>
				<?php printf('%.4f',$be_diff_wt); ?>
			</td>
			<td> Sum of indiv. wts.:</td><td>
				<?php printf('%.4f',$be_tot_wt); ?>
			</td>
			<td> Difference:</td><td>
				<?php printf('%.4f',$be_diff); ?>
			</td>
		</tr>
		
		<tr class=arial10>
			<td>Al carrier: </td>
			<td> Final less initial wt:</td>
			<td><?php printf('%.4f',$al_diff_wt); ?></td>
			<td> Sum of indiv. wts.:</td>
			<td><?php printf('%.4f',$al_tot_wt); ?></td>
			<td> Difference:</td>
			<td><?php printf('%.4f',$al_diff); ?></td>
		</tr>
	</table>

<?=form_close()?>
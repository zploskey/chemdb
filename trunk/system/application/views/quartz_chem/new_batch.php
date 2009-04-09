<p><?=validation_errors()?></p>

<table width="800" class="arial10">

	<?=form_open(site_url('quartz_chem/new_batch'))?>
		<input type=hidden name="id" value="<?=$batch->id?>">
		<input type=hidden name="start_date" value="<?=$batch->start_date?>">
	
		<tr>
			<td width="400"> Batch ID </td>
			<td><?=$batch->id?></td>
		</tr>
		<tr>
			<td>
				Batch start date:
			</td>
			<td><?=$batch->start_date?></td>
		</tr>
		<tr>
			<td>
				Number of samples:
			</td>
			<td>
				<?php if ($allow_num_edit): ?>
					<input type="text" name="numsamples" size=50 value="<?=$numsamples?>">
				<?php else: ?>
					<input type="hidden" name="numsamples" size=50 value="<?=$numsamples?>">
					<?=$numsamples?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
				Your initials:
			</td>
			<td>
				<input type="text" name="owner" size=50 value="<?=$batch->owner?>">
			</td>
		</tr>
		<tr>
			<td>Short batch description:</td>
			<td>
				<textarea name="description" rows=2 cols=50><?=$batch->description?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan=2 align=center><hr>
				<p><input type=submit value="Save and refresh"></p>
				<hr>
			</td>
		</tr>
	<?=form_close()?>
 

	<tr>
		<?=form_open(site_url('quartz_chem/load_samples'))?>
			<input type="hidden" name="id" value="<?=$batch->id?>">
			<td colspan="2" align="center">
				<p>
					<input type="submit" value="Onward to sample loading and carrier addition">
				</p>
				<hr>
			</td>
		<?=form_close()?>
	</tr>
</table>

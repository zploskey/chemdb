<?=form_open('alchecks/add_solution_weights', '', 
    array( // hidden variables
        'refresh' => 'true',
        'batch_id' => $batch->id)
)?>

Batch ID: <?=$batch->id?><br/>
Batch date: <?=$batch->prep_date?><br/>
Batch owner: <?=$batch->owner?><br/>
Number of samples: <?=$nsamples?><br/>
Batch description: <?=$batch->description?><br/>

<?
if ($errors) {
    echo '<hr>' . validation_errors();
} 
?>

<table width="800">
	<tr>
		<td align=center>
			<hr><p/>
			<input type=submit value="Save and refresh">
    		<p/><hr>
		</td>
	</tr>
</table>

<table width=800 class=xl24>
    <tr>
        <td align=center>No.</td>
        <td>Sample Name</td>
        <td align=center>Bkr. ID</td>
        <td align=center>Sample wt.</td>
        <td align=center>Bkr + soln.</td>
        <td align=center>Soln. wt.</td>
        <td align=center>Add'l DF</td>
        <td align=center>Total DF</td>
        <td align=left>Notes</td>
    </tr>
    <tr><td colspan="9"><p><hr></p></td></tr>

<? for ($a = 0; $a < $nsamples; $a++): 
    $an = $batch['AlcheckAnalysis'][$a];
?>
	 
	<tr>
	    <td align=center><?=$an['number_within_batch']?></td>
	    <td><?=$sample_name[$a]?></td>
	    <td align="center"><?=$an['bkr_number']?></td>
    	<td align="center"><?=$sample_wt[$a]?></td>
         <td align="center">
            <input type="text" name="wt_bkr_soln[]" value="<?=$an->wt_bkr_soln?>" size="8"> 
	    </td>
	    <td><?=sprintf('%.4f', $soln_wt[$a])?></td>
    	<td align=center>
    	    <input type="text" size="2" name='addl_dil_factor[]' value="<?=$an->addl_dil_factor?>" size="5"> 
    	</td>
    	<td align=center><?=sprintf('%.2f', $tot_df[$a])?></td>
    	<td><input type="text" name="notes[]" value="<?=$an->notes?>" size="36"></td>
	</tr>
	
<? endfor; ?>

</table>

<table width=800>
    <tr><td></td></tr>
    <tr>
        <td align=center><hr>
            <p><input type="submit" value="Save and refresh"></p>
            <p><?=anchor('alchecks', "Looks good -- I'm done")?></p>
            <p><hr></p>
        </td>
    </tr>
</table>

<?=form_close()?>
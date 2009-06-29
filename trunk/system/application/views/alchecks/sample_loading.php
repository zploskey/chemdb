<?=form_open('alchecks/sample_loading', '', 
    array( // hidden variables
        'refresh' => 'true',
        'batch_id' => $batch->id)
)?>

Batch ID: <?=$batch->id?><br/>
Batch date: <?=$batch->prep_date?><br/>
Batch owner: <?=$batch->owner?><br/>
Number of samples: <?=$numsamples?><br/>
Batch description: <?=$batch->description?><br/>

<table width="800">
	<tr>
		<td align=center>
			<hr><p/>
			<input type=submit value="Save and refresh">
        	<input type=submit value="Add a sample" name="add">
    		<p/><hr>
		</td>
	</tr>
</table>

<table width=800 class=xl24>
    <tr>
        <td align=center>No.</td>
        <td>Analysis ID</td>
        <td>Sample name</td>
        <td align=center>Bkr. ID</td>
        <td>Bkr. tare wt.</td>
        <td>Bkr + sample</td>
        <td align=center>Sample wt.</td>
        <td align=left>Notes</td>
    </tr>
    <tr><td colspan=8><p><hr></p></td></tr>

<? for ($a=0; $a < $numsamples; $a++): ?>
	 
	<tr>
	    <td>
	        <?=$number_within_batch[$a]?>
	        <?=form_hidden('number_within_batch[]', $number_within_batch[$a])?>
	    </td>
	    <td>
	        <?=$analysis_id[$a]?>
    	    <?=form_hidden('analysis_id[]', $analysis_id[$a])?>
    	</td>
        <td>
            <input type="text" name="sample_name[]" value=<?=$sample_name[$a]?> size="20"> 
	    </td>
    	<td align=center>
    	    <input type="text" size="3" name='bkr_number[]' value="<?=$bkr_number[$a]?>"> 
    	</td>
	    <td>
            <input type="text" size="8" name="wt_bkr_tare[]" value="<?=wt_bkr_tare[$a]?>"> 
	    </td>

    	<td>
    	    <?=form_input(array(
    	        'size' => '8',
    	        'name' => 'wt_bkr_sample[]', 
    	        'value' => $wt_bkr_sample[$a]))
			?>
    	</td>
	
    	<td align=center><? printf('%.4f', $wt_sample[$a]); ?></td>
    	
    	<td align=left>
    	    <?=form_input(array(
    	        'name' => 'notes[]',
    	        'value' => $notes,
    	        'size' => 20))?>
    	</td>	
	</tr>
	
<? endfor; ?>

</table>

<table width=800>
    <tr><td></td></tr>
    <tr>
        <td align=center><hr>
            <p><input type="submit" value="Save and refresh"></p>
            <p><input type="submit" value="Add a sample" name="add"></p>
            <p><?=anchor('alchecks', "Looks good -- I'm done")?></p>
            <p><hr></p>
        </td>
    </tr>
</table>

<?=form_close()?>
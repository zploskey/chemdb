<?=form_open('alchecks/sample_loading', '', 
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
        <td align="center">
            <hr><p/>
            <input type="submit" value="Save and refresh">
            <input type="submit" value="Add a sample" name="add">
            <p/><hr>
        </td>
    </tr>
</table>

<table width="800" class="xl24">
    <tr>
        <td align="center">No.</td>
        <td>Analysis ID</td>
        <td>Sample name</td>
        <td align="center">Bkr. ID</td>
        <td>Bkr. tare wt.</td>
        <td>Bkr + sample</td>
        <td align="center">Sample wt.</td>
        <td align="left">Notes</td>
    </tr>
    <tr><td colspan="8"><p><hr></p></td></tr>

<? for ($a = 0; $a < $nsamples; $a++): 
    $an = $batch->AlcheckAnalysis[$a];
?>
     
    <tr>
        <td>
            <?=$an->number_within_batch?>
        </td>
        <td>
            <?=$an->id?>
        </td>
        <td>
            <input type="text" name="sample_name[]" class="sample_name" value="<?=$sample_name[$a]?>" size="20"> 
        </td>
        <td align="center">
            <input type="text" size="3" name='bkr_number[]' value="<?=$an->bkr_number?>"> 
        </td>
        <td>
            <input type="text" size="8" name="wt_bkr_tare[]" value="<?=$an->wt_bkr_tare?>"> 
        </td>

        <td>
            <?=form_input(array(
                'size' => '8',
                'name' => 'wt_bkr_sample[]', 
                'value' => $an->wt_bkr_sample))?>
        </td>
    
        <td align="center"><? printf('%.4f', $wt_sample[$a]); ?></td>
        
        <td align="left">
            <?=form_input(array(
                'name' => 'notes[]',
                'value' => $an->notes,
                'size' => 20))?>
        </td>   
    </tr>
    
<? endfor; ?>

</table>

<table width="800">
    <tr><td></td></tr>
    <tr>
        <td align="center"><hr>
            <p><input type="submit" value="Save and refresh"></p>
            <p><input type="submit" value="Add a sample" name="add"></p>
            <p><?=anchor('alchecks', "Looks good -- I'm done")?></p>
            <p><hr></p>
        </td>
    </tr>
</table>

<?=form_close()?>
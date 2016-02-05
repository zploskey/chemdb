<?php echo form_open('alchecks/add_solution_weights', '',
    array( // hidden variables
        'refresh' => 'true',
        'batch_id' => $batch->id)
); ?>

Batch ID: <?php echo $batch->id; ?><br/>
Batch date: <?php echo $batch->prep_date; ?><br/>
Batch owner: <?php echo $batch->owner; ?><br/>
Number of samples: <?php echo $nsamples; ?><br/>
Batch description: <?php echo $batch->description; ?><br/>

<?php
if ($errors) {
    echo '<hr>' . validation_errors();
}
?>

<table width="800">
    <tr>
        <td align="center">
            <hr><p/>
            <input type=submit value="Save and refresh">
            <p/><hr>
        </td>
    </tr>
</table>

<table width="800" class="xl24">
    <tr>
        <td align="center">No.</td>
        <td>Sample Name</td>
        <td align="center">Bkr. ID</td>
        <td align="center">Sample wt.</td>
        <td align="center">Bkr + soln.</td>
        <td align="center">Soln. wt.</td>
        <td align="center">Add'l DF</td>
        <td align="center">Total DF</td>
        <td align="left">Notes</td>
    </tr>
    <tr><td colspan="9"><p><hr></p></td></tr>

<?php for ($a = 0; $a < $nsamples; $a++):
    $an = $batch['AlcheckAnalysis'][$a];
?>

    <tr>
        <td align="center"><?php echo $an['number_within_batch']; ?></td>
        <td><?php echo $sample_name[$a]; ?></td>
        <td align="center"><?php echo $an['bkr_number']; ?></td>
        <td align="center"><?php echo sprintf('%.4f', $sample_wt[$a]); ?></td>
         <td align="center">
            <input type="text" name="wt_bkr_soln[]" value="<?php echo $an->wt_bkr_soln; ?>" size="8">
        </td>
        <td><?php echo sprintf('%.4f', $soln_wt[$a]); ?></td>
        <td align="center">
            <input type="text" size="2" name='addl_dil_factor[]' value="<?php echo $an->addl_dil_factor; ?>" size="5">
        </td>
        <td align="center"><?php echo sprintf('%.2f', $tot_df[$a]); ?></td>
        <td><input type="text" name="notes[]" value="<?php echo $an->notes; ?>" size="36"></td>
    </tr>

<?php endfor; ?>

</table>

<table width="800">
    <tr><td></td></tr>
    <tr>
        <td align="center"><hr>
            <p><input type="submit" value="Save and refresh"></p>
            <p><?php echo anchor('alchecks', "Looks good -- I'm done"); ?></p>
            <p><hr></p>
        </td>
    </tr>
</table>

<?php echo form_close(); ?>

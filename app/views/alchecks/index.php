<table width="800">

<?php echo form_open('alchecks/new_batch'); ?>
    <tr>
        <td><i><b>Start new batch of Al checks</i></b></td>
        <td class="option_button"><input type="submit" value="Start a new batch"></td>
    </tr>
    <tr><td colspan="2"><hr></p></td></tr>
<?php echo form_close(); ?>

<!--- REVISIT INITIAL WEIGHINGS FOR EXISTING BATCH -->
<?php echo form_open('alchecks/sample_loading', '', array('retrieval' => 'true')); ?>
    <tr>
        <td><i><b>Revisit initial weighings for existing batch</b></i></td>
    </tr>
    <tr>
        <td class="options"><select name="batch_id"><?php echo $allBatchOptions; ?></select></td>
        <td class="option_button"><input type="submit" value="Sample loading for this batch"></td>
    </tr>
    <tr><td colspan="2"><hr></p></td></tr>
<?php echo form_close(); ?>

    <!--- ADD ICP SOLUTION WEIGHTS -->
<?php echo form_open('alchecks/add_solution_weights'); ?>
    <tr>
        <td><i><b>Add ICP solution weights to existing batch</i></b></td>
    </tr>
    <tr>
        <td class="options"><select name="batch_id"><?php echo $allBatchOptions; ?></select></td>
        <td class="option_button"><input type="submit" value="Add ICP solution weights for this batch"></td>
    </tr>
    <tr><td colspan="2"><hr></p></td></tr>
<?php echo form_close(); ?>

<!--- ADD AL-BE-FE-TI-MG CONCENTRATIONS -->
<?php echo form_open('alchecks/add_icp_data'); ?>
    <tr>
        <td><i><b>Add Be-Mg-Al-Ca-Ti-Fe measurements to existing batch</i></b></td>
    </tr>
    <tr>
        <td class="options"><select name="batch_id"><?php echo $allBatchOptions; ?></select></td>
        <td class="option_button"><input type="submit" value="Add ICP results for this batch"></td>
    </tr>
    <tr><td colspan="2"><hr></p></td></tr>
<?php echo form_close(); ?>

<!--- FINAL REPORT -->
<?php echo form_open('alchecks/report'); ?>
    <tr>
        <td><i><b>Report Al check results</i></b></td>
    </tr>
    <tr>
        <td class="options"><select name="batch_id"><?php echo $allBatchOptions; ?></select></td>
        <td class="option_button"><input type="submit" value="Report Al check results for this batch"></td>
    </tr>
    <tr><td colspan="2"><hr></p></td></tr>
<?php echo form_close(); ?>

</table>

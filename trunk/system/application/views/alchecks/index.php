<table width=800>

<?=form_open('alchecks/new_batch')?>
    <tr>
        <td><i><b>Start new batch of Al checks</i></b></td>
        <td class="option_button"><input type="submit" value="Start a new batch"></td>      
    </tr>
    <tr><td colspan=2><hr></p></td></tr>
<?=form_close()?>
    
<!--- REVISIT INITIAL WEIGHINGS FOR EXISTING BATCH -->
<?=form_open('alchecks/sample_loading', '', array('retrieval' => 'true'))?>
    <tr>
        <td><i><b>Revisit initial weighings for existing batch</b></i></td>
    </tr>
    <tr>
        <td class="options"><select name="batch_id"><?=$recentBatchOptions?></select></td>
        <td class="option_button"><input type="submit" value="Sample loading for this batch"></td>
    </tr>
    <tr><td colspan=2><hr></p></td></tr>
<?=form_close()?>
    
    <!--- ADD ICP SOLUTION WEIGHTS -->
<?=form_open('alchecks/add_solution_weights')?>
    <tr>
        <td><i><b>Add ICP solution weights to existing batch</i></b></td>
    </tr>
    <tr>
        <td class="options"><select name="batch_id"><?=$recentBatchOptions?></select></td>
        <td class="option_button"><input type="submit" value="Add ICP solution weights for this batch"></td>
    </tr>
    <tr><td colspan=2><hr></p></td></tr>
<?=form_close()?>

<!--- ADD AL-BE-FE-TI-MG CONCENTRATIONS -->
<?=form_open('alchecks/add_icp_data')?>
    <tr>
        <td><i><b>Add Al-Be-Fe-Ti-Mg measurements to existing batch</i></b></td>
    </tr>
    <tr>
        <td class="options"><select name="batch_id"><?=$recentBatchOptions?></select></td>
        <td class="option_button"><input type="submit" value="Add ICP results for this batch"></td>
    </tr>
    <tr><td colspan=2><hr></p></td></tr>
<?=form_close()?>

<!--- FINAL REPORT -->
<?=form_open('alchecks/report_results')?>
    <tr>
        <td><i><b>Report Al check results</i></b></td>
    </tr>
    <tr>
        <td class="options"><select name="batch_id"><?=$allBatchOptions?></select></td>
        <td class="option_button"><input type="submit" value="Report Al check results for this batch"></td>
    </tr>
    <tr><td colspan=2><hr></p></td></tr>
<?=form_close()?>

</table>
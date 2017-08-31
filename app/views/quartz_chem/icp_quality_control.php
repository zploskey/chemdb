<?php echo form_open(
    'quartz_chem/icp_quality_control',
    '',
    array('batch_id' => $batch['id'], 'refresh' => true)
) // hidden vars?>

<table width="800" class="arial10">
    <tr>
        <td>
        <h3>Batch information:</p></h3>
        Batch ID: <?php echo $batch['id']; ?> <br>
        Batch start date: <?php echo $batch['start_date']; ?> <br>
        Batch owner: <?php echo $batch['owner']; ?> <br>
        Batch description: <?php echo $batch['description']; ?> <br>
        </td>
    </tr>
    <tr>
        <td colspan="4">Batch notes:<br>
            <textarea align="center" name="notes" rows="5" cols="100"><?php echo $batch['notes']; ?></textarea>
        </td>
    </tr>
    <tr><td><hr></td></tr>
</table>

<?php if ($errors) {
    echo validation_errors();
} ?>

<table width="800" class="arial8">
<?php
// save the column labels for repeated printing within the analysis loop
echo $cols = <<<COL
<tr><td colspan="11"><hr></td></tr>
<tr>
    <td>Analysis ID</td>
    <td>Sample name</td>
    <td>Split number</td>
    <td>Split beaker ID</td>
    <td>Run no.</td>
    <td>ICP [Be]</td>
    <td>ug Be</td>
    <td>OK?</td>
    <td>ICP [Al]</td>
    <td>ug Al</td>
    <td>OK?</td>
</tr>
COL;

for ($a = 0; $a < $batch['nsamples']; $a++):
    $an = &$batch['Analysis'][$a]; ?>
    <tr><td colspan="11"><hr></td></tr>
    <tr>
    <?php for ($s = 0; $s < $an['nsplits']; $s++): ?>

        <?php if ($s == 0): ?>
            <td><?php echo $an['id']; ?></td>
            <td><?php echo $an['sample_name']; ?></td>
        <?php else: ?>
            <td colspan="2"></td>
        <?php endif; ?>

        <?php for ($r = 0; $r < $an['Split'][$s]['nruns']; $r++): // ICP run loop
            $run = &$an['Split'][$s]['IcpRun'][$r]; ?>

            <?php if ($r == 0):; ?>
                <td>Split <?php echo $an['Split'][$s]['split_num']; ?></td>
                <td><?php echo $an['Split'][$s]['SplitBkr']['bkr_number']; ?></td>
            <?php else: ?>
                <td colspan="4"></td>
            <?php endif; ?>

            <td>Run <?php echo $run['run_num']; ?></td>
            <td><?php printf('%.4f', $run['be_result']); ?></td>
            <td><?php printf('%.1f', $run['be_tot']); ?></td>
            <td>
                <input type="checkbox" value="<?php echo $run['id']; ?>" name="use_be[]" <?php echo ($run['use_be'] == 'y') ? 'checked' : ''; ?>>
            </td>
            <td><?php printf('%.4f', $run['al_result']); ?></td>
            <td><?php printf('%.1f', $run['al_tot']); ?></td>
            <td>
                <input type="checkbox" value="<?php echo $run['id']; ?>" name="use_al[]" <?php echo ($run['use_al'] == 'y') ? 'checked' : ''; ?>>
            </td>
        </tr>
        <?php endfor; // runs;?>

    <?php endfor; // splits?>

    <!-- Row 6: Averages -->
    <tr>
        <td colspan="5"></td>
        <td>Average ug:</td>
        <td colspan="2">
            <?php printf('%.1f', $an['be_avg']); ?>
            &plusmn;
            <?printf('%.1f', $an['be_sd']); ?>
        </td>
        <td></td>

        <td colspan="2">
            <?php printf('%.1f', $an['al_avg']); ?>
            &plusmn;
            <?php printf('%.1f', $an['al_sd']); ?>
        </td>
    </tr>

    <!-- ROW 7. PERCENT UNCERTAINTY -->

    <tr>
        <td colspan="5"></td>
        <td>Percent error:</td>
        <td colspan="2"><?php printf('%.1f', $an['be_pct_err']); ?></td>
        <td></td>
        <td colspan="2"><?php printf('%.1f', $an['al_pct_err']); ?></td>
    </tr>

    <tr>
        <td colspan="5"></td>
        <td>Pct. recovery:</td>
        <td colspan="2"><?php printf('%.1f', $an['be_recovery']); ?></td>
        <td></td>
        <td colspan="2"><?php printf('%.1f', $an['al_recovery']); ?></td>
    </tr>

    <?php if (($a % 2) != 0): ?>
        <tr><td colspan="11"><hr></td></tr>
        <tr>
            <td colspan="11" align="center">
                <input type="submit" value="Save and refresh">
            </td>
        </tr>
        <?php if ($a != $batch['nsamples'] - 1) {
                echo $cols;
            } ?>
    <?php endif; ?>

<?php endfor; // analyses?>

<tr><td colspan="11"><hr></td></tr>

</table>

<?php echo form_close(); ?>

<?php echo form_open('quartz_chem/index'); ?>
    <div align="center">
        <input type="submit" value="I'm done -- back to main menu">
    </div>
<?php echo form_close(); ?>

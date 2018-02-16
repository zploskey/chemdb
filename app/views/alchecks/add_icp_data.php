<?php echo form_open('alchecks/add_icp_data', '', array('batch_id' => $batch->id, 'refresh' => true)); ?>

Batch ID: <?php echo $batch->id; ?><br/>
Batch date: <?php echo $batch->prep_date; ?><br/>
ICP date: <input type="text" name="icp_date" value="<?php echo $batch->icp_date; ?>"><br>
Batch owner: <?php echo $batch->owner; ?><br/>
Number of samples: <?php echo $nsamples; ?><br/>
Batch description: <?php echo $batch->description; ?><br/>

<?php echo $errors ? '<hr>'.validation_errors() : ''; ?>

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
        <td>Sample name</td>
        <td align="center">Bkr. ID</td>

        <td align="center">ICP<br>[Be]</td>
        <td align="center">ICP<br>[Mg]</td>
        <td align="center">ICP<br>[Al]</td>
        <td align="center">ICP<br>[Ca]</td>
        <td align="center">ICP<br>[Ti]</td>
        <td align="center">ICP<br>[Fe]</td>


        <td align="center">Qtz<br>[Be]</td>
        <td align="center">Qtz<br>[Mg]</td>
        <td align="center">Qtz<br>[Al]</td>
        <td align="center">Qtz<br>[Ca]</td>
        <td align="center">Qtz<br>[Ti]</td>
        <td align="center">Qtz<br>[Fe]</td>
        <td align="left">Notes</td>
    </tr>

<?php
for ($a = 0; $a < $nsamples; $a++):
    $an = $batch['AlcheckAnalysis'][$a];
?>

    <tr>
        <td align="center"><?php echo $an['number_within_batch']; ?></td>
        <td><?php echo $sample_name[$a]; ?></td>
        <td align="center"><?php echo $an['bkr_number']; ?></td>
        <td align="center"><input type=text name=icp_be[] value=<?php echo $an['icp_be']; ?> size=4></td>
        <td align="center"><input type=text name=icp_mg[] value=<?php echo $an['icp_mg']; ?> size=4></td>
        <td align="center"><input type=text name=icp_al[] value=<?php echo $an['icp_al']; ?> size=4></td>
        <td align="center"><input type=text name=icp_ca[] value=<?php echo $an['icp_ca']; ?> size=4></td>
        <td align="center"><input type=text name=icp_ti[] value=<?php echo $an['icp_ti']; ?> size=4></td>
        <td align="center"><input type=text name=icp_fe[] value=<?php echo $an['icp_fe']; ?> size=4></td>
        <td align="center"><?php echo sprintf('%.2f', $qtz_be[$a]); ?></td>
        <td align="center"><?php echo sprintf('%.2f', $qtz_mg[$a]); ?></td>
        <td align="center"><?php echo sprintf('%.2f', $qtz_al[$a]); ?></td>
        <td align="center"><?php echo sprintf('%.2f', $qtz_ca[$a]); ?></td>
        <td align="center"><?php echo sprintf('%.2f', $qtz_ti[$a]); ?></td>
        <td align="center"><?php echo sprintf('%.2f', $qtz_fe[$a]); ?></td>
        <td align="left"><input type=text name=notes[] value="<?php echo $an['notes']; ?>"></td>
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

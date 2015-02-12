<?php
echo form_open('alchecks/add_icp_data', '',
               array('batch_id' => $batch->id,
                     'refresh'  => true));
?>

Batch ID: <?=$batch->id?><br/>
Batch date: <?=$batch->prep_date?><br/>
ICP date: <input type="text" name="icp_date" value="<?=$batch->icp_date?>"><br>
Batch owner: <?=$batch->owner?><br/>
Number of samples: <?=$nsamples?><br/>
Batch description: <?=$batch->description?><br/>

<?php if ($errors) echo '<hr>' . validation_errors(); ?>

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
        <td align="center"><?=$an['number_within_batch']?></td>
        <td><?=$sample_name[$a]?></td>
        <td align="center"><?=$an['bkr_number']?></td>
        <td align="center"><input type=text name=icp_be[] value=<?=$an['icp_be']?> size=4></td>
        <td align="center"><input type=text name=icp_mg[] value=<?=$an['icp_mg']?> size=4></td>
        <td align="center"><input type=text name=icp_al[] value=<?=$an['icp_al']?> size=4></td>
        <td align="center"><input type=text name=icp_ca[] value=<?=$an['icp_ca']?> size=4></td>
        <td align="center"><input type=text name=icp_ti[] value=<?=$an['icp_ti']?> size=4></td>
        <td align="center"><input type=text name=icp_fe[] value=<?=$an['icp_fe']?> size=4></td>
        <td align="center"><?=sprintf('%.2f', $qtz_be[$a])?></td>
        <td align="center"><?=sprintf('%.2f', $qtz_mg[$a])?></td>
        <td align="center"><?=sprintf('%.2f', $qtz_al[$a])?></td>
        <td align="center"><?=sprintf('%.2f', $qtz_ca[$a])?></td>
        <td align="center"><?=sprintf('%.2f', $qtz_ti[$a])?></td>
        <td align="center"><?=sprintf('%.2f', $qtz_fe[$a])?></td>


        <td align="left"><input type=text name=notes[] value="<?=$an['notes']?>"></td>
    </tr>

<?php endfor; ?>

</table>

<table width="800">
    <tr><td></td></tr>
    <tr>
        <td align="center"><hr>
            <p><input type="submit" value="Save and refresh"></p>
            <p><?=anchor('alchecks', "Looks good -- I'm done")?></p>
            <p><hr></p>
        </td>
    </tr>
</table>

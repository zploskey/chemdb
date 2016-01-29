<?php echo form_open('alchecks/quick_add'); ?>

<? if ($batch_id): // a batch has already been created, keep track of its id ?>
    <input type="hidden" name="batch_id" value="<?php echo $batch_id; ?>">
    <? if ($analysis): ?>
        <input type="hidden" name="analysis_id" value="<?php echo $analysis->id; ?>">
    <? endif; ?>
<? endif; ?>

<table width="800" class="arial10">

<tr>
    <td>Sample name:
        <input type="text" size="20" name="sample_name"
         value="<?php echo $analysis->sample_name; ?>">
    </td>

    <td>[Al] (ppm in qtz):
        <input type="text" size="7" name="icp_al" value="<?php echo $analysis->icp_al; ?>">
    </td>

    <td>[Fe] (ppm in qtz):
        <input type=text size="7" name="icp_fe" value="<?php echo $analysis->icp_fe; ?>">
    </td>

    <td>[Ti] (ppm in qtz):
        <input type="text" size="7" name="icp_ti" value="<?php echo $analysis->icp_ti; ?>">
    </td>
</tr>

<tr>
    <td colspan="4"><hr>
    <?
    if ($errors) {
        echo validation_errors() . '<hr>';
    }
    ?>
    </td>
</tr>



<tr>
    <td colspan="4" align="center">
        <input type="submit" name="refresh" value="Save and refresh">
    </td>
</tr>

</table>

<table width="800"><tr><td><hr></td></tr>
    <tr>
        <td align="center">
        <input type="submit" name="close" value="Save and close">
        </td>
    </tr>
    <tr><td><hr></td></tr>
</table>

<?php echo form_close(); ?>

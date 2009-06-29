<?php
echo form_open('alchecks/sample_loading', '', 
    array('refresh' => true,
          'batch_id' => $batch_id));

echo validation_errors();
?>
<table width=800>
    <tr>
        <td>Today's date:</td>
        <td align=left>
            <input type="hidden" name="batch[prep_date]" value="<?=date('Y-m-d')?>" size="50">
            <?=date('r')?>
        </td>
    </tr>
    <tr>
        <td>Number of samples in batch:</td>
        <td>
            <? if ($allow_num_edit): ?>
                <input type="text" name="numsamples" size="50">
            <? else: ?>
                <?=$numsamples?>
            <? endif; ?>
        </td>
    </tr>
    <tr>
        <td>Your initials:</td>
        <td><?=form_input('batch[owner]', $batch->owner)?></td>
    </tr>
    <tr>
        <td>Batch description:</td>
        <td>
        <textarea name="batch[description]" rows="5" cols="50"><?=$batch->description?></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center"><hr><p/>
            <input type=submit value="Hit it!">
            </p><hr>
        </td>
    </tr>
</table>
<?=form_close()?>
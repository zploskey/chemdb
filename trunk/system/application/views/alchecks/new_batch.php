<?php
echo form_open('alchecks/new_batch', '', 
    array('batch_id' => $batch->id,
          'refresh'  => true));

echo validation_errors();
?>
<table width="800">
    <tr>
        <td>Today's date:</td>
        <td align="left">
            <input type="hidden" name="prep_date" value="<?=date('Y-m-d')?>" size="50">
            <?=date('r')?>
        </td>
    </tr>
    <tr>
        <td>Number of samples in batch:</td>
        <td>
            <? if ($allow_num_edit): ?>
                <input type="text" name="numsamples" size="50">
            <? else: ?>
                <?=$nsamples?>
            <? endif; ?>
        </td>
    </tr>
    <tr>
        <td>Your initials:</td>
        <td><?=form_input('owner', $batch->owner)?></td>
    </tr>
    <tr>
        <td>Batch description:</td>
        <td>
        <textarea name="description" rows="5" cols="50"><?=$batch->description?></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center"><hr><p/>
            <input type="submit" value="Hit it!">
            </p><hr>
        </td>
    </tr>
</table>
<?=form_close()?>
<p><?=anchor('alchecks', "Looks good -- I'm done")?></p>
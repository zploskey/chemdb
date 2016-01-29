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
            <input type="hidden" name="prep_date" value="<?php echo date('Y-m-d'); ?>" size="50">
            <?php echo date('r'); ?>
        </td>
    </tr>
    <tr>
        <td>Number of samples in batch:</td>
        <td>
            <? if ($allow_num_edit): ?>
                <input type="text" name="numsamples" size="50">
            <? else: ?>
                <?php echo $nsamples; ?>
            <? endif; ?>
        </td>
    </tr>
    <tr>
        <td>Your initials:</td>
        <td><?php echo form_input('owner', $batch->owner); ?></td>
    </tr>
    <tr>
        <td>Batch description:</td>
        <td>
        <textarea name="description" rows="5" cols="50"><?php echo $batch->description; ?></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center"><hr><p/>
            <input type="submit" value="Save and Refresh">
            </p><hr>
        </td>
    </tr>
</table>
<?php echo form_close(); ?>

<? if (!is_null($batch->id)): ?>

<?php echo form_open('alchecks/sample_loading', '', array('batch_id'=>$batch->id)); ?>
<input type="submit" value="Begin loading samples">
<?php echo form_close(); ?>

<? endif; ?>

<p align="center"><?php echo anchor('alchecks', "Back to options"); ?></p>

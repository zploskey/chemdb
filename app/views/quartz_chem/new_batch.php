<p><?php echo validation_errors(); ?></p>

<table width="800" class="arial10">

    <?php echo form_open('quartz_chem/new_batch'); ?>
        <input type="hidden" name="batch_id" value="<?php echo $batch->id; ?>">
        <input type="hidden" name="start_date" value="<?php echo $batch->start_date; ?>">
        <input type="hidden" name="is_refresh" value="true">
        <tr>
            <td width="400"> Batch ID </td>
            <td><?php echo $batch->id; ?></td>
        </tr>
        <tr>
            <td>Batch start date: </td>
            <td><?php echo $batch->start_date; ?></td>
        </tr>
        <tr>
            <td>Number of samples (including blanks): </td>
            <td>
            <?php if ($allow_num_edit): ?>
                <input type="text" name="numsamples" size="50" value="<?php echo $numsamples; ?>">
            <?php else: ?>
                <input type="hidden" name="numsamples" size="50" value="<?php echo $numsamples; ?>">
                <?php echo $numsamples; ?>
            <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Your initials: </td>
            <td>
                <input type="text" name="owner" size="50" value="<?php echo $batch->owner; ?>">
            </td>
        </tr>
        <tr>
            <td>Short batch description: </td>
            <td>
                <textarea name="description" rows="2" cols="50"><?php echo $batch->description; ?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center"><hr>
                <p><input type="submit" value="Save and refresh"></p>
                <hr>
            </td>
        </tr>
    <?php echo form_close(); ?>

    <?php echo form_open('quartz_chem/load_samples'); ?>
        <tr>
            <input type="hidden" name="batch_id" value="<?php echo $batch->id; ?>">
            <td colspan="2" align="center">
                <p>
                    <input type="submit" value="Onward to sample loading and carrier addition">
                </p>
                <hr>
            </td>
        </tr>
     <?php echo form_close(); ?>
</table>

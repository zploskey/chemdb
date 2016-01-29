<?php echo form_open(site_url('quartz_chem/add_solution_weights'), '',
    array('batch_id' => $batch->id, 'is_refresh' => 'TRUE')); ?>

<table width="800" class="arial10">
    <tr>
        <td>
        <h3>Batch information:<br/></h3>
        Batch ID: <?php echo $batch->id; ?> <br/>
        Batch start date: <?php echo $batch->start_date; ?><br/>
        Batch owner: <?php echo $batch->owner; ?><br/>
        Batch description:  <?php echo $batch->description; ?><p/>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            Batch notes:<br>
            <center>
                <textarea name=batch[notes] rows=5 cols=100><?php echo $batch->notes; ?></textarea>
            </center>
        </td>
    </tr>
    <tr><td><hr></td></tr>
</table>

<?php if ($errors): ?>
    <?php echo validation_errors(),'<hr>'; ?>
<?php endif; ?>

<table width="800" class="arial8">
    <tr>
        <td colspan="6" class="arial12">Sample information:<p/></td>
    </tr>
    <tr>
        <td>Analysis ID</td>
        <td>Sample name</td>
        <td>Dissolution bottle ID</td>
        <td>Bottle tare wt.</td>
        <td>Total wt. bottle and sol'n </td>
        <td>Wt. HF sol'n</td>
    </tr>

    <!-- MAIN SAMPLE LOOP -->
    <?php for ($i = 0; $i < $numsamples; $i++): ?>

        <tr><td colspan="10"><hr></td></tr>
        <tr>
            <td><?php echo $batch->Analysis[$i]->id; ?></td>
            <td><?php echo $batch->Analysis[$i]->sample_name; ?></td>
            <td>
                <?php echo $batch->Analysis[$i]->DissBottle->bottle_number; ?>
            </td>
            <td><?php echo $batch->Analysis[$i]->wt_diss_bottle_tare; ?></td>
            <td>
                <input type=text size=16 name="wt_diss_bottle_total[]" value="<?php echo $batch->Analysis[$i]->wt_diss_bottle_total; ?>">
            </td>
            <td>
                <?php
                $weight = $batch->Analysis[$i]->wt_diss_bottle_total - $batch->Analysis[$i]->wt_diss_bottle_tare;
                printf('%.4f', $weight);
                ?>
            </td>
        </tr>

    <?php endfor; ?>

    <tr><td colspan="6"><hr></td></tr>
</table>

    <table width="800">
        <tr>
            <td align="center">
                <input type="submit" value="Save and refresh">
            </td>
        </tr>
        <tr><td><hr></td></tr>
    </table>
<?php echo form_close(); ?>

<?php echo $this->load->view('quartz_chem/bottom_links'); ?>

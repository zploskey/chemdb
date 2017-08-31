<?php echo form_open('quartz_chem/add_icp_results'); ?>
<input type="hidden" name="batch_id" value="<?php echo $batch->id; ?>">
<table width="800" class="arial10">
    <?php $this->load->view('tr_main_link_hr'); ?>
    <tr>
        <td>
        <h3>Batch information:</p></h3>
        Batch ID: <?php echo $batch->id; ?> <br>
        Batch start date: <?php echo $batch->start_date; ?> <br>
        Batch owner: <?php echo $batch->owner; ?> <br>
        Batch description: <?php echo $batch->description; ?> <br>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            Batch notes:<br>
            <center>
            <textarea name="notes" rows="5" cols="100"><?php echo $batch->notes; ?></textarea>
            </center>
        </td>
    </tr>
    <tr><td><hr></td></tr>
</table>
<p>
    Data should be input as:<br><br>
    Beaker#1 icprun1 run2 run3 ... runN<br>
    Beaker#2 icprun1 run2 run3 ... runN<br><br>
    i.e.:<br>
    AB1 0.8363 0.8387 0.8356<br>
    AB2 0.9878 0.9504 0.9764 <br><br>
    This is most easily done by pasting from Excel.<br><br>
    <b>IMPORTANT NOTE:</b> If you make changes here you <em>must</em> redo ICP Quality Control if you want any of the icp results to be ignored in calculations.<br>
</p>
<table width="800">
    <tr>
        <td><b>Aluminum:</b></td>
        <td><b>Beryllium:</b></td>
    </tr>
    <tr>
        <td><textarea name="al_text" rows="<?php echo $nrows; ?>" cols="48"><?php echo $al_text; ?></textarea></td>
        <td><textarea name="be_text" rows="<?php echo $nrows; ?>" cols="48"><?php echo $be_text; ?></textarea></td>
    </tr>
</table>
<hr>
<p align="center"><?php echo form_submit('submit', 'Save and refresh'); ?></p>
<hr>
<?php echo form_close(); ?>

<?php $this->load->view('quartz_chem/bottom_links'); ?>

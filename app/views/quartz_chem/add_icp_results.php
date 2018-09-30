<?php echo form_open('quartz_chem/add_icp_results'); ?>
<input type="hidden" name="batch_id" value="<?php echo $batch->id; ?>">
<div id="batch-info">
  <?php $this->load->view('tr_main_link_hr'); ?>
  <h3>Batch Information</h3>
    <hr>
    Batch ID: <?php echo $batch->id; ?> <br>
    Batch start date: <?php echo $batch->start_date; ?> <br>
    Batch owner: <?php echo $batch->owner; ?> <br>
    Batch description: <?php echo $batch->description; ?> <br>
    <label for="notes">Batch notes:</label>
    <textarea id="notes" name="notes" rows="5"><?php echo $batch->notes; ?></textarea>
    <hr>
</div>
<p>Data should be input as:</p>
<pre>
Beaker#1 icprun1 run2 run3 ... runN
Beaker#2 icprun1 run2 run3 ... runN
</pre>
<p>For example:</p>
<pre>
AB1 0.8363 0.8387 0.8356
AB2 0.9878 0.9504 0.9764
</pre>
<p>This is most easily done by pasting from Excel.</p>
<p>
  <b>IMPORTANT NOTE:</b>
  If you make changes here you <em>must</em> redo ICP Quality Control if you want any of the icp results to be ignored in calculations.
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
<div class="center"><?php echo form_submit('submit', 'Save and refresh'); ?></div>
<hr>
<?php echo form_close(); ?>

<?php $this->load->view('quartz_chem/bottom_links'); ?>

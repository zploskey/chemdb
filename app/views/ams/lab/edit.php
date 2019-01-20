
<h2><?php echo $subtitle; ?>
  (<?php echo anchor("ams/lab/view/$lab->id", 'View'); ?>)
</h2>

<?php echo form_open('ams/lab/edit/' . $lab->id); ?>

<div class="lab edit">
  <div class="formblock">
    <label for="lab[name]">Name:</label>
    <?php echo form_input('lab[name]', $lab->name); ?>
  </div>

  <div class="formblock">
    <label for="lab[full_name]">Full Name:</label>
    <?php echo form_input('lab[full_name]', $lab->full_name); ?>
  </div>

  <div class="formblock"><?php echo validation_errors(); ?></div>

  <div class="formblock center">
    <?php echo form_submit('save', 'Save'); ?>
  </div>
</div>

<?php echo form_close(); ?>

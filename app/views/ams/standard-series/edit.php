
<h2><?php echo $subtitle; ?>
  <?php if ($series->id): ?>
    (<?php echo anchor("ams/standard-series/view/$element/$series->id", 'View'); ?>)
  <?php endif; ?>
</h2>

<?php echo form_open("ams/standard-series/edit/$element/$series->id"); ?>

<div class="lab edit">
  <div class="formblock">
    <label for="series[code]">Code:</label>
    <?php echo form_input('series[code]', $series->code); ?>
  </div>

  <div class="formblock">
    <label for="series[notes]">Notes:</label>
    <?php echo form_input('series[notes]', $series->notes); ?>
  </div>

  <div class="formblock"><?php echo validation_errors(); ?></div>

  <div class="formblock center">
    <?php echo form_submit('save', 'Save'); ?>
  </div>
</div>

<?php echo form_close(); ?>

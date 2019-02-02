<h2><?php echo $subtitle; ?>
  (<?php echo anchor("ams/standard-series/edit/$element/$series->id", 'Edit'); ?>)
</h2>
<div class="standardseries view">
  <div class="formblock">
    <label for="code">Code:</label>
    <span name="code"><?php echo $series->code; ?></span>
  </div>
  <div class="formblock">
    <label for="notes">Notes:</label>
    <span name="notes"><?php echo $series->notes; ?></span>
  </div>
</div>

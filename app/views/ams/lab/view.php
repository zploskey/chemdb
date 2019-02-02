<h2><?php echo $subtitle; ?>
  (<?php echo anchor("ams/lab/edit/$lab->id", 'Edit'); ?>)
</h2>
<div class="lab view">
  <div class="formblock">
    <label for="name">Name:</label>
    <span name="name"><?php echo $lab->name; ?></span>
  </div>
  <div class="formblock">
    <label for="full_name">Full Name:</label>
    <span name="full_name"><?php echo $lab->full_name; ?></span>
  </div>
</div>

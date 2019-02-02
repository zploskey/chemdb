
<p class="center"><?php
    echo anchor("ams/standard-series/add/$element", "Add $element Standard Series");
?></p>
<table class="itemlist">
    <tr>
        <th>ID</th>
        <th class="name">Code</th>
        <th>Actions</th>
    </tr>
<?php foreach ($all_series as $series): ?>
    <tr>
        <td><?php echo $series->id; ?></td>
        <td class="name"><?php echo $series->code; ?></td>
        <td>
            <?php echo anchor("ams/standard-series/view/$element/$series->id", 'View'); ?>
            <?php echo anchor("ams/standard-series/edit/$element/$series->id", 'Edit'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>


<p class="center"><?php echo anchor('ams/lab/add', 'Add Lab'); ?></p>
<table class="itemlist">
    <tr>
        <th>ID</th>
        <th class="name">Name</th>
        <th>Actions</th>
    </tr>
<?php foreach ($labs as $lab): ?>
    <tr>
        <td><?php echo $lab->id; ?></td>
        <td class="name"><?php echo $lab->name; ?></td>
        <td>
            <?php echo anchor("ams/lab/view/$lab->id", 'View'); ?>
            <?php echo anchor("ams/lab/edit/$lab->id", 'Edit'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>


<table class="itemlist">
    <tr>
        <th>ID</th>
        <th class="name">Name</th>
        <th>Full Name</th>
    </tr>
<?php foreach ($labs as $lab): ?>
    <tr>
        <td><?php echo $lab->id; ?></td>
        <td class="name"><?php echo $lab->name; ?></td>
        <td><?php echo $lab->full_name; ?></td>
    </tr>
<?php endforeach; ?>
</table>

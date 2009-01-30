<!-- This file isn't used, use index.php instead. -->

<p>
<span id="actionbar"><ul>
	<li><?php echo anchor('projects/add','Add Project'); ?> |</li>
	<li><?php echo anchor('welcome', 'Return to Main Menu'); ?></li>
</ul></span>
</p>

<div class="data">
    <table style="width: 500px">
        <tr><th>Name</th><th>Actions</th></tr>

        <p><?php echo 'count was='.$this->project->where('name', 'testing2')->count(); ?></p>

        <?php foreach($projects->result() as $proj): ?>
        <tr align="center">
            <td><?php echo $proj->name; ?></td>
            <td>
                <span id="actionbar">
                    <ul>
                        <li><?php echo anchor('projects/edit/'.$proj->id, 'Edit'); ?></li>
                        <li><?php echo anchor('projects/delete/'.$proj->id, 'Delete'); ?></li>
                    </ul>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
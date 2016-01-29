<?php $this->load->view('projects/nav'); ?>
<p><h2><?php echo $subtitle; ?> (<?php echo anchor('projects/edit/'.$proj->id, 'Edit'); ?>)</h2></p>
<br>
<div class="data">
    <table>
        <tr>
            <td align="right"><em>Name:&nbsp;</em></td>
            <td><?php echo $proj->name; ?></td>
        </tr>
        <tr>
            <td align="right"><em>Date Added:&nbsp;</em></td>
            <td><?php echo $proj->date_added; ?></td>
        </tr>
        <tr>
            <td align="right"><em>Description:&nbsp;</em></td>
            <td><?php echo $proj->description; ?></td>
        </tr>
    </table>
    <br><br>
    <div class="data">
    <p>
        <table class="itemlist">
            <tr>
                <th>Associated Samples &nbsp;&nbsp;&nbsp;</th>
                <th>Actions</th>
            </tr>
            <?php if ($proj->Sample->count()): ?>
                <?php foreach ($proj->Sample as $s): ?>
                    <tr>
                        <td><?php echo $s->name; ?></td>
                        <td>
                            <span id="actionbar">
                                <ul>
                                    <li><?php echo anchor('samples/view/'.$s->id, 'View'); ?></li>
                                    <li><?php echo anchor('samples/edit/'.$s->id, 'Edit'); ?></li>
                                </ul>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td>None</td><td></td></tr>
            <?php endif; ?>
        </table>
    </p>
</div>

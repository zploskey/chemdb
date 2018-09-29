<?php $this->load->view('samples/nav'); ?>
<?php echo form_open('samples/index'); ?>
    Sample search:
    <input type="text" class="sample_name" value="<?php echo htmlentities($query); ?>" name="query">
    <input type="submit" value="Search">
<?php echo form_close(); ?>
<br>

<?php if ($samples->count() > 0): ?>

    <?php if ($paginate): ?>
        <div class="pagination">
            <?php echo $pagination; ?>
        </div>
    <?php endif; ?>
    <div class="data">
        <p>
            <table class="itemlist">
                <tr>
                    <th class="name">
                        <?php echo anchor("samples/index/$sort_by/$alt_sort_dir/0", 'Name'); ?>
                    </th>
                    <th>Actions</th>
                </tr>

                <?php foreach ($samples as $s): ?>

                    <tr>
                        <td class="name"><?php echo $s->name; ?></td>
                        <td>
                            <span class="actionbar">
                                <ul>
                                    <li><?php echo anchor('samples/view/'.$s->id, 'View'); ?></li>
                                    <li><?php echo anchor('samples/edit/'.$s->id, 'Edit'); ?></li>
                                </ul>
                            </span>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </table>
        </p>
    </div>
    <?php if ($paginate): ?>
        <div class="pagination">
            <?php echo $pagination; ?>
        </div>
    <?php endif; ?>

<?php else: ?>

    <br>
    No sample matching '<em><?php echo htmlentities($query); ?></em>' could be found.<br><br>
    Would you like to <?php echo anchor('samples/edit', 'add'); ?> it?

<?php endif; ?>

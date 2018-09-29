<?php if (!$type): ?>

    <br>
    <h2>Select the type of container to manage:</h2>
    <br>
    <ul>
        <li>
            <?php echo anchor('containers/index/DissBottle', 'Dissolution Bottles'); ?>
        </li>
        <li>
            <?php echo anchor('containers/index/SplitBkr', 'Split Beakers'); ?>
        </li>
    </ul>

<?php else: ?>

    <?php $this->load->view('containers/nav'); ?>
    <br>

    <?php if ($containers->count() > 0): ?>

        <div class="data">
            <table class="itemlist">
                <tr>
                    <th>
                        <?php echo anchor("containers/index/$type/id/$alt_sort_dir", 'ID'); ?>
                    </th>
                    <th class="name">
                        <?php echo anchor("containers/index/$type/number/$alt_sort_dir", 'Number'); ?>
                    </th>
                    <th>Actions</th>
                </tr>

                <?php foreach ($containers as $c): ?>

                    <tr>
                        <td><?php echo $c->id; ?></td>
                        <td class="name"><?php echo $c->$number; ?></td>
                        <td>
                            <span class="actionbar">
                                <ul>
                                    <li><?php echo anchor("containers/view/$type/".$c->id, 'View'); ?></li>
                                    <li><?php echo anchor("containers/edit/$type/".$c->id, 'Edit'); ?></li>
                                </ul>
                            </span>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </table>
        </div>

    <?php else: ?>

        <br>
        No <?php echo strtolower($longname).'s'; ?> found.<br><br>
        Would you like to <?php echo anchor("containers/edit/$type", 'add'); ?> one?

    <?php endif; ?>

<?php endif; ?>

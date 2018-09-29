<?php if (!$element): ?>

    <br>
    <h2>Select the type of carrier to manage:</h2>
    <br>
    <ul>
        <li>
            <?php echo anchor('carriers/index/al', 'Aluminum'); ?>
        </li>
        <li>
            <?php echo anchor('carriers/index/be', 'Beryllium'); ?>
        </li>
    </ul>

<?php else: ?>

    <?php $this->load->view('carriers/nav'); ?>
    <br>

    <?php if ($carriers->count() > 0): ?>

        <div class="data">
            <table class="itemlist">
                <tr>
                    <th>
                        <?php echo anchor("carriers/index/$element/id/$alt_sort_dir", 'ID'); ?>
                    </th>
                    <th class="name">
                        <?php echo anchor("carriers/index/$element/name/$alt_sort_dir", 'Name'); ?>
                    </th>
                    <th>Actions</th>
                </tr>

                <?php foreach ($carriers as $c): ?>

                    <tr>
                        <td><?php echo $c->id; ?></td>
                        <td class="name"><?php echo $c->name; ?></td>
                        <td>
                            <span class="actionbar">
                                <ul>
                                    <li><?php echo anchor("carriers/view/$element/".$c->id, 'View'); ?></li>
                                    <li><?php echo anchor("carriers/edit/$element/".$c->id, 'Edit'); ?></li>
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
        Would you like to <?php echo anchor("carriers/edit/$element", 'add'); ?> one?

    <?php endif; ?>

<?php endif; ?>

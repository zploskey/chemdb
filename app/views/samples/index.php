<?=$this->load->view('samples/nav')?>
<?=form_open('samples/index')?>
    Sample search:
    <input type="text" class="sample_name" value="<?=htmlentities($query)?>" name="query">
    <input type="submit" value="Search">
<?=form_close()?>
<br>

<? if ($samples->count() > 0): ?>

    <?php if ($paginate): ?>
        <div class="pagination">
            <?=$pagination?>
        </div>
    <?php endif; ?>
    <div class="data">
        <p>
            <table class="itemlist">
                <tr>
                    <th>
                        <?=anchor("samples/index/$sort_by/$alt_sort_dir/0", 'Name')?>
                    </th>
                    <th>Actions</th>
                </tr>

                <?php foreach($samples as $s): ?>

                    <tr>
                        <td id="name"><?=$s->name?></td>
                        <td>
                            <span id="actionbar">
                                <ul>
                                    <li><?=anchor('samples/view/'.$s->id, 'View')?></li>
                                    <li><?=anchor('samples/edit/'.$s->id, 'Edit')?></li>
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
            <?=$pagination?>
        </div>
    <?php endif; ?>

<? else: ?>

    <br>
    No sample matching '<em><?=htmlentities($query)?></em>' could be found.<br><br>
    Would you like to <?=anchor('samples/edit', 'add')?> it?

<? endif; ?>
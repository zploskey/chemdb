<div id="navbar">
    <?=anchor('welcome','Return to Main Menu')?> | <?=anchor('projects','Projects')?>
</div>

<p><h2><?=$subtitle?></h2></p>

<?=form_open(site_url("projects/edit/$arg"))?>
    <div class="formblock">
        <label>Name:</label>
        <input type="text" name="name" value="<?=$proj->name?>"/><br>

        <label>Description:</label>
        <textarea name="description" rows="5" cols="50" wrap="soft"><?=$proj->description?></textarea><br>
    </div>
    <br>
    <p><?=form_error('name')?></p>
    <br>
    <p><input type="submit" value="Submit" /></p>
    <br><br>
    <div class="data">
    <p>
        <table class="itemlist">
            <tr>
                <th>Associated Samples</th>
                <th>Actions</th>
            </tr>
            <? foreach ($samples as $s): ?>
                <tr>
                    <td><?=$s->name?></td>
                    <td>
                        <span id="actionbar">
                            <ul>
                                <li><?=anchor('samples/view/'.$s->id, 'View')?></li>
                                <li><?=anchor('samples/edit/'.$s->id, 'Edit')?></li>
                            </ul>
                        </span>
                    </td>
                </tr>
            <? endforeach;?>
        </table>
    </p>
    </div>
<?=form_close()?>
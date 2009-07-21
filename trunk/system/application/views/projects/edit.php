<div id="navbar">
    <?=anchor('welcome','Return to Main Menu')?> | <?=anchor('projects','Projects')?>
</div>
<br>

<p>
    <h2><?=$subtitle?> (<?=anchor("projects/view/$proj->id", 'View')?>)</h2> 
</p>
<br>

<?=form_open(site_url("projects/edit/$arg"))?>
    <input type="hidden" name="is_refresh" value="TRUE">
    <div class="formblock">
        <label>Name:</label>
        <input type="text" name="name" value="<?=$proj->name?>"/><br>

        <label>Description:</label>
        <textarea name="description" rows="5" cols="50" wrap="soft"><?=$proj->description?></textarea><br>
    </div>
    <br>
    <input type="submit" value="Submit" />
    <br><?=validation_errors()?><br>
    <div class="data">
        <table class="itemlist">
            <tr>
                <th>Associated Samples</th>
                <th>Actions</th>
            </tr>
            <? foreach ($proj->Sample as $s): ?>
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
            <tr>
                <td>Add a sample:</td>
                <td><input type="text" class="sample_name" name="samp"></td>
            </td></tr>
        </table>
    </div>
<?=form_close()?>
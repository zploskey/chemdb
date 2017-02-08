<?php $this->load->view('projects/nav'); ?>
<p>
    <h2><?php echo $subtitle; ?> (<?php echo anchor("projects/view/$proj->id", 'View'); ?>)</h2>
</p>
<br>

<?php echo form_open(site_url("projects/edit/$arg")); ?>
    <input type="hidden" name="is_refresh" value="TRUE">
    <div class="formblock">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo $proj->name; ?>"/>
    </div>
    <div class="formblock">
        <label>Description:</label>
        <textarea name="description" rows="5" cols="50" wrap="soft"><?php echo $proj->description; ?></textarea>
    </div>
    <br>
    <input type="submit" value="Submit" />
    <br><?php echo validation_errors(); ?><br>
    <div class="data">
        <table class="itemlist">
            <tr>
                <th>Associated Samples</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($proj->Sample as $s): ?>
                <tr>
                    <td><?php echo $s->name; ?></td>
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
            <tr>
                <td>Add a sample:</td>
                <td><input type="text" class="sample_name" name="samp"></td>
            </td></tr>
        </table>
    </div>
<?php echo form_close(); ?>

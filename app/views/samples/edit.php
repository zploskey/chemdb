<?php $this->load->view('samples/nav'); ?>
<p><h2>
    <?php echo $subtitle; ?>
    <?php if (!is_null($sample->id)): ?>
        (<?php echo anchor('samples/view/'.$sample->id, 'View'); ?>)
    <?php endif; ?>
</h2></p>

<br>
<?php echo validation_errors(); ?>
<br>

<?php echo form_open(site_url("samples/edit/$arg")); ?>
    <input type="hidden" name="is_refresh" value="TRUE">
    <div class="vEntry">
        <table>
            <tr>
                <th>Name:</th>
                <td><?php echo form_input('sample[name]', $sample->name); ?></td>
            </tr>

            <tr>
                <th>Associated Projects:</th>
                <td>
                    <?php
                    foreach ($projOptions as $po) {
                        echo '<select name="proj[]">', $po, "</select>\n<br/>\n";
                    }
                    ?>
                    <button id="add_select">Link a new project</button>
                </td>
            </tr>

            <tr>
                <th>Latitude:</th>
                <td>
                    <input type="text" name="sample[latitude]" value="<?php echo (float)$sample->latitude; ?>" size="10" /> &nbsp;
                    North latitudes are positive. South latitudes are negative.
                </td>
            </tr>

            <tr>
                <th>Longitude:</th>
                <td>
                    <input type="text" name="sample[longitude]" value="<?php echo (float)$sample->longitude; ?>" size="10" /> &nbsp;
                    East longitudes are positive. West longitudes are negative.
                </td>
            </tr>

            <tr>
                <th>Altitude:</th>
                <td><input type="text" name="sample[altitude]" value="<?php echo (float)$sample->altitude; ?>" size="10" /> &nbsp;m</td>
            </tr>

            <tr>
                <th>Antarctic?:</th>
                <td>
                    &nbsp;
                    <input type="checkbox" name="sample[antarctic]" value="1"
                        <?php echo ($sample->antarctic) ? 'checked' : ''; ?>
                    > &nbsp;
                    Is this an Antarctic sample?
                </td>
            </tr>

            <tr>
                <th>Shield factor:</th>
                <td><input type="text" name="sample[shield_factor]" value="<?php echo (float)$sample->shield_factor; ?>" size="10" /></td>
            </tr>

            <tr>
                <th>Depth (top):</th>
                <td><input type="text" name="sample[depth_top]" value="<?php echo (float)$sample->depth_top; ?>" size="10" /> &nbsp;cm</td>
            </tr>

            <tr>
                <th>Depth (bottom):</th>
                <td><input type="text" name="sample[depth_bottom]" value="<?php echo (float)$sample->depth_bottom; ?>" size="10" /> &nbsp;cm</td>
            </tr>

            <tr>
                <th>Density:</th>
                <td><input type="text" name="sample[density]" value="<?php echo (float)$sample->density; ?>" size="10" /> &nbsp;g/cm<sup>3</sup></td>
            </tr>

            <tr>
                <th>Erosion Rate:</th>
                <td><input type="text" name="sample[erosion_rate]" value="<?php echo (float)$sample->erosion_rate; ?>" size="10" /> &nbsp;cm/y</td>
            </tr>

            <tr>
                <th>Notes:</th>
                <td><textarea name="sample[notes]" rows="5" cols="50" wrap="soft"><?php echo $sample->notes; ?></textarea></td>
            </tr>
        </table>
    <p><input type="submit" value="Submit" /></p>
    </div>
<?php echo form_close(); ?>

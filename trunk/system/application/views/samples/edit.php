<div id="navbar">
    <li><?=anchor('welcome', 'Return to Main Menu')?> | </li>
    <li><?=anchor('samples', 'Samples List')?> | </li>
    <li><?=anchor('samples/edit', 'Add a Sample')?></li>
</div>
<br>

<p><h2>
    <?=$subtitle?> 
    <? if (!is_null($sample->id)): ?>
        (<?=anchor('samples/view/'.$sample->id, 'View')?>)
    <? endif; ?>
</h2></p>
<br>

<?=form_open(site_url("samples/edit/$arg"))?>
    <input type="hidden" name="is_refresh" value="TRUE">
    <p><?=validation_errors()?></p><p/>
    <div class="vEntry">
        <table>
            <tr>
                <th>Name:</th>
                <td><?=form_input('sample[name]', $sample->name)?></td>
            </tr>

            <tr>
                <th>Associated Projects:</th>
                <td>
                    <?php
                    foreach ($projOptions as $po) {
                        $first = false;
                        echo '<select name="proj[]">', $po, '</select><br/>';
                    }
                    ?>
                    <button id="add_select">Link a new project</button>
                </td>
            </tr>

            <tr>
                <th>Latitude:</th>
                <td>
                    <input type="text" name="sample[latitude]" value="<?=$sample->latitude?>" size="10" /> &nbsp;
                    North latitudes are positive. South latitudes are negative.
                </td>
            </tr>

            <tr>
                <th>Longitude:</th>
                <td>
                    <input type="text" name="sample[longitude]" value="<?=$sample->longitude?>" size="10" /> &nbsp;
                    East longitudes are positive. West longitudes are negative. 
                </td>
            </tr>

            <tr>
                <th>Altitude:</th>
                <td><input type="text" name="sample[altitude]" value="<?=$sample->altitude?>" size="10" /> &nbsp;m</td>
            </tr>

            <tr>
                <th>Antarctic?:</th>
                <td>
                    &nbsp;
                    <input type="checkbox" name="sample[antarctic]" value="1"
                        <?=($sample->antarctic) ? 'checked' : ''?>
                    > &nbsp;
                    Is this an Antarctic sample?
                </td>
            </tr>

            <tr>
                <th>Shield factor:</th>
                <td><input type="text" name="sample[shield_factor]" value="<?=$sample->shield_factor?>" size="10" /></td>
            </tr>

            <tr>
                <th>Depth (top):</th>
                <td><input type="text" name="sample[depth_top]" value="<?=$sample->depth_top?>" size="10" /> &nbsp;cm</td>
            </tr>

            <tr>
                <th>Depth (bottom):</th>
                <td><input type="text" name="sample[depth_bottom]" value="<?=$sample->depth_bottom?>" size="10" /> &nbsp;cm</td>
            </tr>

            <tr>
                <th>Density:</th>
                <td><input type="text" name="sample[density]" value="<?=$sample->density?>" size="10" /> &nbsp;g/cm^3</td>
            </tr>

            <tr>
                <th>Erosion Rate:</th>
                <td><input type="text" name="sample[erosion_rate]" value="<?=$sample->erosion_rate?>" size="10" /> &nbsp;cm/y</td>
            </tr>

            <tr>
                <th>Notes:</th>
                <td><textarea name="sample[notes]" rows="5" cols="50" wrap="soft"><?=$sample->notes?></textarea></td>
            </tr>
        </table>
    <p><input type="submit" value="Submit" /></p>
    </div>
<?=form_close()?>
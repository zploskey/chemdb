<?=form_open(site_url('quartz_chem/add_split_weights'))?>

<input type=hidden name="batch_id" value="<?=$batch->id?>">
<input type=hidden name="is_refresh" value="true">

<table width=800 class=arial10>
	<tr>
		<td>
		<h3>Batch information:</h3><p/>
		Batch ID: <?=$batch->id?><br>
		Batch start date: <?=$batch->start_date?><br>
		Batch owner: <?=$batch->owner?><br>
		Batch description: <?=$batch->description?><p/>
		</td>
	</tr>
	<tr>
		<td colspan=4>
			Batch notes:<br>
            <center>
                <textarea name="batch[notes]" rows=5 cols=100><?=$batch->notes?></textarea>
            </center>
		</td>
	</tr>
	<tr><td><hr></td></tr>
    <?php if ($errors): ?>
        <tr><td colspan="4"><?=validation_errors()?></td></tr>
    <?php endif; ?>
</table>

<table width=800 class=arial8>
    <tr>
        <td colspan=8 class=arial12>Sample information:<p/></td>
    </tr>
    <tr>
        <td>Analysis ID</td>
        <td>Sample name</td>
        <td>Dissolution bottle ID</td>
        <td>Split number</td>
        <td>Split beaker ID</td>
        <td>Beaker tare wt.</td>
        <td>Beaker + split wt.</td>
        <td>Split wt.</td>
    </tr>

<?php for ($i = 0; $i < $numsamples; $i++): // main sample loop ?>
	<tr><td colspan=8><hr></td></tr>
        <?php for ($s = 0; $s < $batch->Analysis[$i]->Split->count(); $s++): ?>
            <tr>
            <?php if ($s == 0): ?>
                <td><?=$batch->Analysis[$i]->id?></td>
                <td>
                <?php
                if ($batch->Analysis[$i]->Sample->name != NULL) {
                    echo $batch->Analysis[$i]->Sample->name;
                } else {
                    echo $batch->Analysis[$i]->sample_name;
                }
                ?>
                </td>
                <td><?=$batch->Analysis[$i]->DissBottle->bottle_number?></td>
            <?php else: ?>
                <td colspan=3></td>
            <?php endif; ?>
            <td>Split <?=$s+1?>:</td>
            <td><select name="split_bkr[]">
                <?php
                foreach ($bkr_list as $b) {
                    echo "<option value=$b->id ";
                    if ($b->id == $batch->Analysis[$i]->Split[$s]->split_bkr_id) {
                        echo 'selected';
                    }
                    echo "> $b->bkr_number </option>\n";
                }
                ?>
            </select></td>

            <td>
                <?=form_input('bkr_tare[]', $batch->Analysis[$i]->Split[$s]->wt_split_bkr_tare)?>
            </td>
            <td>
                <?=form_input('bkr_split[]', $batch->Analysis[$i]->Split[$s]->wt_split_bkr_split)?>
            </td>
            <td>
                <?php
                // calculate and print split weight
                $split_wt = $batch->Analysis[$i]->Split[$s]->wt_split_bkr_split
                    - $batch->Analysis[$i]->Split[$s]->wt_split_bkr_tare;
                printf('%.4f', $split_wt);
                ?>
            </td>
        </tr>
        <?php endfor; // split loop ?>
<?php endfor; // sample loop ?>

    <tr><td colspan=8><hr></td></tr>
</table>

<table width=800>
    <tr>
    <td align=center>
    <input type=submit value="Save and refresh">
    </td>
    </tr>
    <tr><td><hr></td></tr>
</table>

<?=form_close()?>

<?=$this->load->view('quartz_chem/bottom_links')?>
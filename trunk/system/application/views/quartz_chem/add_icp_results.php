<?=form_open('quartz_chem/add_icp_results')?>
<input type="hidden" name="batch_id" value="<?=$batch->id?>">
<table width=800 class=arial10>
	<tr>
		<td> 
		<h3>Batch information:</p></h3>
		Batch ID: <?=$batch->id?> <br>
		Batch start date: <?=$batch->start_date?> <br>
		Batch owner: <?=$batch->owner?> <br>
		Batch description: <?=$batch->description?> <br>
		</td>
	</tr>
	<tr>
		<td colspan=4>
			Batch notes:<br>
			<center>
			<textarea name="notes" rows="5" cols="100"><?=$batch->notes?></textarea>
			</center>
		</td>
	</tr>
	<tr><td><hr></td></tr>
</table>
<p>
    Data should be input as:<br><br>
    Beaker#1 icprun1 run2 run3 ... runN<br>
    Beaker#2 icprun1 run2 run3 ... runN<br><br>
    i.e.:<br>
    AB1 0.8363 0.8387 0.8356<br>
    AB2 0.9878 0.9504 0.9764 <br><br>
    This is most easily done by pasting from Excel.<br>
</p>
<table width="800">
    <tr>
        <td><b>Aluminum:</b></td>
        <td><b>Beryllium:</b></td>
    </tr>
    <tr> 
        <td><textarea name="al_text" rows="20" cols="48"><?=$al_text?></textarea></td>
        <td><textarea name="be_text" rows="20" cols="48"><?=$be_text?></textarea></td>
    </tr>
</table>
<p><?=form_submit('submit','Save and refresh')?></p>
<?=form_close()?>
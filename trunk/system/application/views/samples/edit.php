<div id="navbar">
	<li><?=anchor('welcome', 'Return to Main Menu')?> | </li>
	<li><?=anchor('samples', 'Samples List')?> | </li>
	<li><?=anchor('samples/edit', 'Add a Sample')?></li>
</div>

<p><h2><?=$subtitle?></h2></p>

<?=form_open(site_url("samples/edit/$arg"))?>
	<p><?=validation_errors()?></p><p/>

	<div class="formblock">
		<label>Name:</label>
		<?=form_input('name', $sample->name)?><br>
				
		<label>Latitude:</label>
		<input type="text" name="latitude" value="<?=$sample->latitude?>" size="10" /><br>

		<label>Longitude:</label>
		<input type="text" name="longitude" value="<?=$sample->longitude?>" size="10" /><br>
		
		<label>Altitude:</label>
		<input type="text" name="altitude" value="<?=$sample->altitude?>" size="10" /> &nbsp;m<br>

		<label>Shield factor:</label>
		<input type="text" name="shield_factor" value="<?=$sample->shield_factor?>" size="10" /><br>

		<label>Depth (top):</label>
		<input type="text" name="depth_top" value="<?=$sample->depth_top?>" size="10" /> &nbsp;cm<br>

		<label>Depth (bottom):</label>
		<input type="text" name="depth_bottom" value="<?=$sample->depth_bottom?>" size="10" /> &nbsp;cm<br>

		<label>Density:</label>
		<input type="text" name="density" value="<?=$sample->density?>" size="10" /> &nbsp;g/cm^3<br>

		<label>Erosion Rate:</label>
		<input type="text" name="erosion_rate" value="<?=$sample->erosion_rate?>" size="10" /> &nbsp;cm/y<br>

		<label>Notes:</label>
		<textarea name="notes" rows="5" cols="50" wrap="soft"><?=$sample->notes?></textarea><br>
	</div>
	
	<p><input type="submit" value="Submit" /></p>
<?=form_close()?>
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
	<p><?=form_error('name')?></p>
	<p><input type="submit" value="Submit" /></p>
<?=form_close()?>
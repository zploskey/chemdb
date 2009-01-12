<?=anchor('welcome','Main')?> | <?=anchor('projects/list','Projects')?>
<p><h2>Editing <?=$project->name?>, ID=<?=$project->id?></h2></p>

<p><?php echo validation_errors(); ?></p>

<?=form_open('projects')?>
<p\>
Name: 
<input type="text" name="name" value="<?=$project->name?>" size="50">
<input type="submit" value="Submit">
</form>
<p\>
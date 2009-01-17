<?=print_r($project)?>
<?=anchor('welcome','Main')?> | <?=anchor('projects','Projects')?>
<p><h2>Editing <?=$project->name?>, ID=<?=$project->id?></h2></p>

<p><?php echo validation_errors(); ?></p>

<?=form_open('projects/edit_action')?>

<?=form_hidden('id', $this->uri->segment(3)) ?>
<?=form_hidden('submit', true) ?>

<p>Name: <input type="text" name="name" value="<?=$project->name?>" size="50" />

<input type="submit" value="Submit" />
</form>
<p\>
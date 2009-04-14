<div id="navbar">
	<ul>
		<li><?=anchor('welcome', 'Return to Main Menu')?></li> |
		<li><?=anchor('samples/edit','Add Sample')?></li>
	</ul>
</div>

<div class="pagination">
	<?=$pagination?>
</div>
<div class="data">
	<p>
		<table class="itemlist">
			<tr>
				<th><?=anchor("samples/index/$sort_by/$alt_sort_dir/$page", 'Name')?></th>
				<th>Actions</th>
			</tr>
		
			<?php foreach($samples as $s): ?>
		
				<tr>
					<td id="name"><?=$s->name?></td>
					<td>
						<span id="actionbar">
							<ul>
								<li><?=anchor('samples/view/'.$s->id, 'View')?></li>
								<li><?=anchor('samples/edit/'.$s->id, 'Edit')?></li>
							</ul>
						</span>
					</td>
				</tr>
		
			<?php endforeach; ?>
		
		</table>
	</p>
</div>
<div class="pagination">
	<?=$pagination?>
</div>
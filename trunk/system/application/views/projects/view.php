<div id="navbar">
	<ul>
		<li><?=anchor('welcome','Return to Main Menu')?> |</li>
		<li><?=anchor('projects','Projects List')?></li>
	</ul>
</div>

<p><h2><?=$subtitle?> (<?=anchor('projects/edit/'.$project->id,'Edit')?>)</h2></p>

<div id="data">
	<table>
		<tr>
			<td align="right">Name:</td>
			<td><?=$project->name?></td>
		</tr>
		<tr>
			<td align="right">Description:</td>
			<td><?=$project->description?></td>
		</tr>
	</table>
</div>
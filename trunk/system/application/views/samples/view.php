<div id="navbar">
	<ul>
		<li><?=anchor('welcome','Return to Main Menu')?> |</li>
		<li><?=anchor('samples','Samples List')?> |</li>
		<li><?=anchor('samples/edit', 'Add a Sample')?></li>
	</ul>
</div>

<p><h2><?=$subtitle?> (<?=anchor('samples/edit/'.$sample->id,'Edit')?>)</h2></p>

<div id="data">
	<table>
		<tr>
			<td align="right">Name:</td>
			<td><?=$sample->name?></td>
		</tr>
		<tr>
			<td align="right">Latitude:</td>
			<td><?=$sample->latitude?></td>
		</tr>
		<tr>
			<td align="right">Longitude:</td>
			<td><?=$sample->longitude?></td>
		</tr>
		<tr>
			<td align="right">Altitude (m):</td>
			<td><?=$sample->altitude?></td>
		</tr>
		<tr>
			<td align="right">Shield factor:</td>
			<td><?=$sample->shield_factor?></td>
		</tr>
		<tr>
			<td align="right">Depth Top (m):</td>
			<td><?=$sample->depth_top?></td>
		</tr>
		<tr>
			<td align="right">Depth Bottom (m):</td>
			<td><?=$sample->depth_bottom?></td>
		</tr>
		<tr>
			<td align="right">Density (kg/m^3):</td>
			<td><?=$sample->density?></td>
		</tr>
	</table>
</div>
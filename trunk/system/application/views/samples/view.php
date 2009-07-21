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
            <td align="right">Name: &nbsp;</td>
            <td><?=$sample->name?></td>
        </tr>
        <tr>
            <td align="right">Associated Projects: &nbsp;</td>
            <td>
            <?
            if (isset($projects)) {
                foreach ($projects as $p) {
                    echo anchor('projects/view/'.$p['id'], $p['name']) . '<br/>';
                }
            }
            ?>
            </td>
        </tr>
        <tr>
            <td align="right">Latitude: &nbsp;</td>
            <td><?=$sample->latitude?></td>
        </tr>
        <tr>
            <td align="right">Longitude: &nbsp;</td>
            <td><?=$sample->longitude?></td>
        </tr>
        <tr>
            <td align="right">Altitude (m): &nbsp;</td>
            <td><?=$sample->altitude?></td>
        </tr>
        <tr>
            <td align="right">Shield factor: &nbsp;</td>
            <td><?=$sample->shield_factor?></td>
        </tr>
        <tr>
            <td align="right">Depth Top (cm): &nbsp;</td>
            <td><?=$sample->depth_top?></td>
        </tr>
        <tr>
            <td align="right">Depth Bottom (cm): &nbsp;</td>
            <td><?=$sample->depth_bottom?></td>
        </tr>
        <tr>
            <td align="right">Density (g/cm^3): &nbsp;</td>
            <td><?=$sample->density?></td>
        </tr>
        <tr>
            <td align="right">Erosion Rate (cm/y): &nbsp;</td>
            <td><?=$sample->erosion_rate?></td>
        </tr>
        <tr>
            <td align="right">Notes: &nbsp;</td>
            <td><?=$sample->notes?></td>
        </tr>
    </table>
</div>
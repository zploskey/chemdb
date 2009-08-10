<div id="navbar">
    <ul>
        <li><?=anchor('welcome','Return to Main Menu')?> |</li>
        <li><?=anchor('samples','Samples List')?> |</li>
        <li><?=anchor('samples/edit', 'Add a Sample')?></li>
    </ul>
</div>
<br>
<p><h2><?=$subtitle?> (<?=anchor('samples/edit/'.$sample->id,'Edit')?>)</h2></p>
<br>
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
<br/><br/>

<? if (isset($sample->Analysis)): ?>
    <h3>Analyses of this sample</h3>
    <table>
        <tr>
            <th>Batch ID</th>
            <th width="60%">Notes</th>
            <th>AMS ID</th>
            <th>Actions</th>
        </tr>
    <? for ($i = 0; $i < $sample->Analysis->count(); $i++): 
        $an = $sample->Analysis[$i]; ?>
        
        <tr>
            <td align="center"><?=$an->Batch->id?></td>
            <td><?=$an->notes?></td>
            
        <? for ($ams = 0; $ams < $an->BeAms->count(); $ams++): ?>
        
            <? if ($ams == 0): ?>
                <td align="center">
            <? else: ?>
                <tr><td colspan="2"></td><td align="center">
            <? endif; ?>

            <td><?=$ams->id?></td>
            <td>
                <form method="post" target="outputwindow" action="http://hess.ess.washington.edu/cgi-bin/matweb">
                    <input type="submit" value="Calculate exposure age">
                    <input type="hidden" name="requesting_ip" value="<?=getRealIp()?>">
                    <input type="hidden" name="mlmfile" value="al_be_age_many_v22" >
                    <input type="hidden" name="text_block" value="<?=$an_text[$i][$ams]?>">
                </form>
                <form method="post" target="outputwindow" action="http://hess.ess.washington.edu/cgi-bin/matweb">
                    <input type="submit" value="Calculate erosion rate">
                    <input type="hidden" name="requesting_ip" value="<?=getRealIp()?>">
                    <input type="hidden" name="mlmfile" value="al_be_erosion_many_v22" >
                    <input type="hidden" name="text_block" value="<?=$an_text[$i][$ams]?>">
                </form>
            </td>
        </tr>

        <? endfor; // ams loop?>

    <? endfor; // analysis loop?>

    </table>
<? endif; ?>
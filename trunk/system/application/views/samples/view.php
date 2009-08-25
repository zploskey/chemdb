<?=$this->load->view('samples/nav')?>
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
    <table class="">
        <tr>
            <th>Analysis<br>ID</th>
            <th>Batch<br>ID</th>
            <th width="45%">Notes</th>
            <th>Be AMS<br>ID</th>
            <th>Al AMS<br>ID</th>
            <th>Actions</th>
        </tr>
    <? for ($i = 0; $i < $sample->Analysis->count(); $i++): 
        $an = $sample->Analysis[$i]; ?>
        <tr>
            <td><?=$an->id?></td>
            <td align="center"><?=anchor('quartz_chem/final_report/'.$an->Batch->id, $an->Batch->id)?></td>
            <td><?=$an->notes?></td>
            
        <?  $nInputs = (isset($calcInput['exp'][$i])) ? count($calcInput['exp'][$i]) : 0;
            if ($nInputs > 0):
                for ($n = 0; $n < $nInputs; $n++): ?>

                <? if ($n == 0): ?>
                    <td align="center">
                <? else: ?>
                    <tr><td colspan="2"></td><td align="center">
                <? endif; ?>
                    <?=(isset($an['BeAms'][$n]['id']))
                        ? anchor('be_ams/' . $an['BeAms'][$n]['id'], $an['BeAms'][$n]['id']) : '' ?>
                </td>
                <td align="center">
                    <?=(isset($an['AlAms'][$n]['id'])) 
                        ? anchor('al_ams/' . $an['AlAms'][$n]['id'], $an['AlAms'][$n]['id']) : '' ?>
                </td>
                <td>
                    <form method="post" target="outputwindow" action="http://hess.ess.washington.edu/cgi-bin/matweb">
                        <input type="submit" value="Calculate exposure age">
                        <input type="hidden" name="requesting_ip" value="<?=getRealIp()?>">
                        <input type="hidden" name="mlmfile" value="al_be_age_many_v22" >
                        <input type="hidden" name="text_block" value="<?=$calcInput['exp'][$i][$n]?>">
                    </form>
                    <form method="post" target="outputwindow" action="http://hess.ess.washington.edu/cgi-bin/matweb">
                        <input type="submit" value="Calculate erosion rate">
                        <input type="hidden" name="requesting_ip" value="<?=getRealIp()?>">
                        <input type="hidden" name="mlmfile" value="al_be_erosion_many_v22" >
                        <input type="hidden" name="text_block" value="<?=$calcInput['eros'][$i][$n]?>">
                    </form>
                </td>
                </tr>
                <? endfor; // ams loop ?>
            <? else: ?>
                <td colspan="4"></td></tr>
            <? endif; ?>
    <? endfor; // analysis loop ?>

    <tr>
        <td colspan="100%" align="center">
            <form method="post" target="outputwindow" action="http://hess.ess.washington.edu/cgi-bin/matweb">
                <input type="submit" value="Calculate all exposure ages">
                <input type="hidden" name="requesting_ip" value="<?=getRealIp()?>">
                <input type="hidden" name="mlmfile" value="al_be_age_many_v22" >
                <input type="hidden" name="text_block" value="<?=$calcInput['all_exp']?>">
            </form>
            <form method="post" target="outputwindow" action="http://hess.ess.washington.edu/cgi-bin/matweb">
                <input type="submit" value="Calculate all erosion rates">
                <input type="hidden" name="requesting_ip" value="<?=getRealIp()?>">
                <input type="hidden" name="mlmfile" value="al_be_age_many_v22" >
                <input type="hidden" name="text_block" value="<?=$calcInput['all_eros']?>">
            </form>
        </td>
    </tr>
    </table>

<? endif; ?>
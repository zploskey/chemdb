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

<? if ($nAnalyses > 0): ?>

    <h3>Analyses of this sample</h3>
    <br>
    <table>

        <tr>
            <th width="200">Analysis ID</th>
            <? foreach ($sample['Analysis'] as $an): ?>
                <td>
                    <?=$an['id']?>
                </td>
            <? endforeach; ?>
        </tr>
        
        <tr>
            <th>Batch ID</th>
            <? foreach ($sample['Analysis'] as $an): ?>
                <td>
                    <?=anchor('quartz_chem/final_report/'.$an['batch_id'], $an['batch_id'])?>
                </td>
            <? endforeach; ?>
        </tr>
        
        <tr>
            <th>Sample Wt (g)</th>
            <? foreach ($sample['Analysis'] as $an): ?>
                <td>
                    <?=$an->getSampleWt()?>
                </td>
            <? endforeach; ?>
        </tr>
        
        <tr>
            <th>Carrier ID</th>
            <? foreach ($sample['Analysis'] as $an): ?>
                <td>
                    <?=$an['Batch']['BeCarrier']['name']?>
                </td>
            <? endforeach; ?>
        </tr>
        
        <tr>
            <th>Mass Be Carrier (ug)</th>
            <? foreach ($sample['Analysis'] as $an): ?>
                <td>
                    <?=$an['wt_be_carrier'] * 1e6 // convert to ug from g ?>
                </td>
            <? endforeach; ?>
        </tr>
        
        <tr>
            <th>ICP Mass Be (ug)</th>
            <? for ($i = 0; $i < $nAnalyses; $i++): ?>
                <td>
                    <?=$ugBe[$i]?>
                </td>
            <? endfor; ?>
        </tr>
        
        <tr>
            <th>ICP Mass Al (ug)</th>
            <? for ($i = 0; $i < $nAnalyses; $i++): ?>
                <td>
                    <?=$ugAl[$i]?>
                </td>
            <? endfor; ?>
        </tr>

        
        <tr>
            <th>[Al] (ppm)</th>
            <? for ($i = 0; $i < $nAnalyses; $i++): ?>
                <td><?=$ppmAl[$i]?></td>
            <? endfor; ?>
        </tr>

        <? if ($calcsExist): ?>
            <tr>

                <!-- Calculations for all analyses -->

                <td align="center">
                    <form method="post" target="outputwindow" action="http://hess.ess.washington.edu/cgi-bin/matweb">
                        <input type="submit" value="Calculate all exposure ages">
                        <input type="hidden" name="requesting_ip" value="<?=getRealIp()?>">
                        <input type="hidden" name="mlmfile" value="al_be_age_many_v22" >
                        <input type="hidden" name="text_block" value="<?=$calcInput['all_exp']?>">
                    </form>
                    <form method="post" target="outputwindow" action="http://hess.ess.washington.edu/cgi-bin/matweb">
                        <input type="submit" value="Calculate all erosion rates">
                        <input type="hidden" name="requesting_ip" value="<?=getRealIp()?>">
                        <input type="hidden" name="mlmfile" value="al_be_erosion_many_v22" >
                        <input type="hidden" name="text_block" value="<?=$calcInput['all_ero']?>">
                    </form>
                </td>

                <!-- Calculations for individual analyses -->

                <?  
                for ($i = 0; $i < $nAnalyses; $i++):

                    $nInputs = (isset($calcInput['exp'][$i])) ? count($calcInput['exp'][$i]) : 0;

                    if ($nInputs > 0):

                        for ($n = 0; $n < $nInputs; $n++): 
                ?>

                    <td align="center">
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
                            <input type="hidden" name="text_block" value="<?=$calcInput['ero'][$i][$n]?>">
                        </form>
                    </td>

                <?
                        endfor; 
                    endif;
                endfor; 
                ?>
                
            </tr>
            
        <? endif; ?>

    </table>

<? endif; ?>
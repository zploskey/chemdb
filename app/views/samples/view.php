<?php $this->load->view('samples/nav'); ?>
<p><h2><?php echo $subtitle; ?> (<?php echo anchor('samples/edit/'.$sample->id,'Edit'); ?>)</h2></p>
<br>
<div class="data">
    <table>
        <tr>
            <td align="right">Name: &nbsp;</td>
            <td><?php echo $sample->name; ?></td>
        </tr>
        <tr>
            <td align="right">Associated Projects: &nbsp;</td>
            <td>
            <?php
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
            <td><?php echo $sample->latitude; ?></td>
        </tr>
        <tr>
            <td align="right">Longitude: &nbsp;</td>
            <td><?php echo $sample->longitude; ?></td>
        </tr>
        <tr>
            <td align="right">Altitude (m): &nbsp;</td>
            <td><?php echo $sample->altitude; ?></td>
        </tr>
        <tr>
            <td align="right">Shield factor: &nbsp;</td>
            <td><?php echo $sample->shield_factor; ?></td>
        </tr>
        <tr>
            <td align="right">Depth Top (cm): &nbsp;</td>
            <td><?php echo $sample->depth_top; ?></td>
        </tr>
        <tr>
            <td align="right">Depth Bottom (cm): &nbsp;</td>
            <td><?php echo $sample->depth_bottom; ?></td>
        </tr>
        <tr>
            <td align="right">Density (g/cm<sup>3</sup>): &nbsp;</td>
            <td><?php echo $sample->density; ?></td>
        </tr>
        <tr>
            <td align="right">Erosion Rate (cm/y): &nbsp;</td>
            <td><?php echo $sample->erosion_rate; ?></td>
        </tr>
        <tr>
            <td align="right">Notes: &nbsp;</td>
            <td><?php echo $sample->notes; ?></td>
        </tr>
    </table>
</div>
<br/>

<?php if ($nAnalyses > 0): ?>

    <?php echo form_open('samples/submit_to_calc/'.$sample->id, array('target'=>'outputwindow')); ?>

    <h3>Analyses of this sample:</h3>
    <br>
    <table>

        <tr>
            <th width="200">Analysis ID</th>
            <?php foreach ($sample['Analysis'] as $an): ?>
                <td>
                    <?php echo $an['id']; ?>
                </td>
            <?php endforeach; ?>
        </tr>

        <tr>
            <th>Batch ID</th>
            <?php foreach ($sample['Analysis'] as $an): ?>
                <td>
                    <?php echo anchor('quartz_chem/final_report/'.$an['batch_id'], $an['batch_id']); ?>
                </td>
            <?php endforeach; ?>
        </tr>

        <tr>
            <th>Sample Wt (g)</th>
            <?php foreach ($sample['Analysis'] as $an): ?>
                <td>
                    <?php echo $an->getSampleWt(); ?>
                </td>
            <?php endforeach; ?>
        </tr>

        <tr>
            <th>Carrier ID</th>
            <?php foreach ($sample['Analysis'] as $an): ?>
                <td>
                    <?php echo $an['Batch']['BeCarrier']['name']; ?>
                </td>
            <?php endforeach; ?>
        </tr>

        <tr>
            <th>Mass Be Carrier (ug)</th>
            <?php foreach ($sample['Analysis'] as $an): ?>
                <td>
                    <?php echo sprintf('%.1f',$an['wt_be_carrier'] * $an['Batch']['BeCarrier']['be_conc']); ?>
                </td>
            <?php endforeach; ?>
        </tr>

        <tr>
            <th>ICP Mass Be (ug)</th>
            <?php for ($i = 0; $i < $nAnalyses; $i++): ?>
                <td>
                    <?php echo $ugBe[$i]; ?>
                </td>
            <?php endfor; ?>
        </tr>

        <tr>
            <th>ICP Mass Al (ug)</th>
            <?php for ($i = 0; $i < $nAnalyses; $i++): ?>
                <td>
                    <?php echo $ugAl[$i]; ?>
                </td>
            <?php endfor; ?>
        </tr>

        <tr>
            <th>Be Yield (%)</th>
            <?php for ($i = 0; $i < $nAnalyses; $i++): ?>
                <td><?php echo $yieldBe[$i]; ?></td>
            <?php endfor; ?>
        </tr>

        <?php if ($calcsExist): ?>

            <tr>
                <th>Use which Be measure?</th>
                <?php for ($i = 0; $i < $nAnalyses; $i++): ?>
                    <td>
                        <input type="radio" name="analysis<?php echo $i; ?>" value="icp"> ICP<br/>
                        <input type="radio" name="analysis<?php echo $i; ?>" value="carrier" checked> Carrier
                    </td>
                <?php endfor; ?>
            </tr>

            <tr>
                <th>Include in report?</th>
                <?php
                for ($i = 0; $i < $nAnalyses; $i++) {
                    $ai = $sample->Analysis[$i];
                    if (isset($ai->BeAms[0]->BeAmsStd) || isset($ai->AlAms[0]->AlAmsStd)) {
                        // set flag that at least one analysis can be submitted to calc
                        echo '<td>', form_checkbox('incInReport[]', $i, true), '</td>';
                    }
                }
                ?>
            </tr>

            <tr>
                <!-- Perform calculations for all analyses -->
                <td align="center">
                    <?php echo form_submit('calcSelAge', 'Get Selected Exposure Ages'); ?><br/>
                    <?php echo form_submit('calcSelEro', 'Get Selected Erosion Rates'); ?>
                </td>

                <!-- Calculations for individual analyses -->
<?php
                for ($i = 0; $i < $nAnalyses; $i++) {
                    $ai = $sample->Analysis[$i];
                    if (isset($ai->BeAms[0]->BeAmsStd) || isset($ai->AlAms[0]->AlAmsStd)) {
                        echo '<td>',
                             form_submit('calcAge_'.$i, 'Get Exposure Age'), '<br/>',
                             form_submit('calcEro_'.$i, 'Get Erosion Rate'),
                             '</td>';
                    } else {
                        echo '<td></td>';
                    }
                }
?>

            </tr>

        <?php endif; ?>

    </table>

    <?php echo form_close(); ?>

<?php endif; ?>

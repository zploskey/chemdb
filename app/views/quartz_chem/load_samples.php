<?php echo form_open('quartz_chem/load_samples'); ?>
<input type="hidden" name="batch_id" value="<?php echo $batch->id; ?>">
<input type="hidden" name="is_refresh" value="TRUE">

<table width="800" class="arial10">
    <tr>
        <td colspan="4" width="400">
        <h3>Batch information:</h3><p/>
        Batch ID: <?php echo $batch->id; ?><br>
        Batch start date: <?php echo $batch->start_date; ?> <br>
        Batch owner: <?php echo $batch->owner; ?> <br>
        Batch description: <?php echo $batch->description; ?> <p/>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            Batch notes:<br>
            <center>
                <textarea name="notes" rows=5 cols=100><?php echo $batch->notes; ?></textarea>
            </center>
        </td>
    </tr>
    <tr>
        <td>
            Be carrier:
        </td>
        <td>
            <select name="be_carrier_id">
                <?php echo $be_carrier_options; ?>
            </select>
        </td>
        <td> Al carrier:</td>
        <td>
            <select name="al_carrier_id">
                <?php echo $al_carrier_options; ?>
            </select>
        </td>
    </tr>
    <tr><td></td>
        <td>
            [Be]:
            <?php
            if ($batch->BeCarrier) {
                echo $batch->BeCarrier->be_conc;
                if (!is_null($batch->BeCarrier->del_be_conc)) {
                    echo ' &plusmn; ', $batch->BeCarrier->del_be_conc;
                }
                echo ' ug/g';
            } ?>
        </td>
        <td></td>
        <td>
            [Al]:
            <?php
            if($batch->AlCarrier) {
                echo $batch->AlCarrier->al_conc;
                if (!is_null($batch->AlCarrier->del_al_conc)) {
                    echo ' &plusmn; ', $batch->AlCarrier->del_al_conc;
                }
                echo ' ug/g';
            } ?>
        </td>
    </tr>
    <tr>
        <td>Be carrier previous wt:</td>
        <td>
            <?php if ($be_prev): ?>
                <?php echo $be_prev->wt_be_carrier_final; ?> (<?php echo $be_prev->start_date; ?>)
            <?php endif; ?>
        </td>

        <td>Al carrier previous wt:</td>
        <td>
            <?php if ($al_prev): ?>
                <?php echo $al_prev->wt_al_carrier_final; ?> (<?php echo $al_prev->start_date; ?>)
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>
            Be carrier initial wt:
        </td>
        <td>
            <input type=text name="wt_be_carrier_init" width="10" value="<?php echo $batch->wt_be_carrier_init; ?>">
        </td>
        <td>
            Al carrier initial wt:
        </td>
        <td>
            <input type=text name="wt_al_carrier_init" width="10" value="<?php echo $batch->wt_al_carrier_init; ?>">
        </td>
    </tr>
    <tr>
        <td>
            Be carrier final wt:
        </td>
        <td>
            <input type=text name="wt_be_carrier_final" width="10" value="<?php echo $batch->wt_be_carrier_final; ?>">
        </td>
        <td>
            Al carrier final wt:
        </td>
        <td>
            <input type=text name="wt_al_carrier_final" width="10" value="<?php echo $batch->wt_al_carrier_final; ?>">
        </td>
    </tr>
    <tr><td colspan="4"><hr></td></tr>
</table>

<?php if ($errors) {
    echo validation_errors();
} ?>

<table width="800" class="arial8">
    <tr>
        <td colspan="3" class="arial12">Sample information:</td>
        <td colspan="7" class="arial10">
            <i>Open new window to create Al/Fe/Ti concentrations for samples not in database:
                <?php echo anchor('alchecks/quick_add',
                    'click here',
                    array('target' => 'dummy_alcheck_window')
                ); ?>
            </i>
        </td>
    </tr>
    <tr>
        <td>Analysis ID</td>
        <td>Sample name</td>
        <td>Type</td>
        <td>Diss bottle ID</td>
        <td>Wt. bottle<br> tare</td>
        <td>Wt. bottle <br>and sample</td>
        <td>Wt. sample</td>
        <td>Wt. Be <br> carrier sol'n</td>
        <td>Mass Be </td>
        <td>Wt. Al <br> carrier sol'n</td>
    </tr>

    <!-- Display all the analysis information -->
    <?php for ($i = 0; $i < $num_analyses; $i++): // main display loop ?>
        <tr><td colspan="10"><hr></td></tr>
        <tr>
            <td> <?php echo $batch->Analysis[$i]->id; ?> </td>
            <td>
                <input type="text" class="sample_name" size=16 name="sample_name[]"
                    value="<?php echo $batch->Analysis[$i]->sample_name; ?>">
            </td>
            <!-- Sample type dropdown -->
            <td>
                <select name="sample_type[]" class="type">

                    <?php if (strcmp(trim(strtoupper($batch->Analysis[$i]->sample_type)), 'BLANK') == 0): ?>
                        <option value="SAMPLE">Sample</option>
                        <option value="BLANK" selected>Blank</option>
                    <?php else: ?>
                        <option value="SAMPLE" selected>Sample</option>
                        <option value="BLANK">Blank</option>
                    <?php endif; ?>

                </select>
            </td>

            <!-- Bottle id dropdown -->
            <td>
                <select name="diss_bottle_id[]">
                    <?php echo $diss_bottle_options[$i]; ?>
                </select>
            </td>

            <!-- bottle tare wt -->
            <td>
                <input type=text size=8 name="wt_diss_bottle_tare[]" class="tareWt" value="<?php echo $batch->Analysis[$i]->wt_diss_bottle_tare; ?>">
            </td>

            <!-- bottle + sample weight -->
            <td>
                <input type=text size=8 name="wt_diss_bottle_sample[]" class="sampleWt" value="<?php echo $batch->Analysis[$i]->wt_diss_bottle_sample; ?>">
            </td>

            <!-- Column 7. Sample weight -->
            <td>
                <?php echo sprintf('%.4f', ($batch->Analysis[$i]->wt_diss_bottle_sample - $batch->Analysis[$i]->wt_diss_bottle_tare)); ?>
            </td>

            <!-- Column 8. Be carrier wt -->
            <td>
                <input type=text size=8 name="wt_be_carrier[]" value="<?php echo $batch->Analysis[$i]->wt_be_carrier; ?>">
            </td>

            <!-- Column 9. Be mass -->
            <td>
                <?php
                    if ($batch->BeCarrier) {
                        $be_mass = ($batch->Analysis[$i]->wt_be_carrier * $batch->BeCarrier->be_conc);
                    } else {
                        $be_mass = 0;
                    }
                    printf('%.1f', $be_mass);
                ?>
            </td>

            <!-- Column 10. Al carrier wt -->
            <td>
                <input type=text size=8 name="wt_al_carrier[]" value="<?php echo $batch->Analysis[$i]->wt_al_carrier; ?>">
            </td>

        </tr><tr><td colspan="10"></td></tr>
        <tr>
            <td colspan="4">
                <?php if ($prechecks[$i]['show']): ?>
                    Concentrations in quartz:
                    [Al] = <?php printf('%.1f', $prechecks[$i]['conc_al']); ?>
                    [Fe] = <?php printf('%.1f', $prechecks[$i]['conc_fe']); ?>
                    [Ti] = <?php printf('%.1f', $prechecks[$i]['conc_ti']); ?>
                <?php else: ?>
                    Sample name not in Al checks database
                <?php endif; ?>
            </td>

            <td>Total Al (mg): <br>(incl. carrier)</td>
            <td><?php printf('%.2f', $prechecks[$i]['m_al']); ?></td>
            <td>Total Fe (mg):</td>
            <td><?php printf('%.2f', $prechecks[$i]['m_fe']); ?></td>
            <td>Total Ti (mg):</td>
            <td><?php printf('%.2f', $prechecks[$i]['m_ti']); ?></td>
        </tr>

        <!-- Print save and refresh button every two rows -->
        <?php if ( ($i % 2) != 0): ?>
            <tr>
                <td colspan="11"><hr></td>
            </tr>
            <tr>
                <td colspan="11" align="center">
                    <input type="submit" value="Save and refresh" class="ancBtn" id="ln<?php echo $i; ?>">
                </td>
            </tr>
        <?php endif; ?>
    <?php endfor; // main display loop ?>

    <tr><td colspan="10"><hr></td></tr>
</table>

<table width="800">
    <tr><td class="arial10">Carrier weight comparison:</td></tr>
    <tr class="arial10">
        <td>Be carrier: </td>
        <td> Final less initial wt:</td><td>
            <?php printf('%.4f', $be_diff_wt); ?>
        </td>
        <td> Sum of indiv. wts.:</td><td>
            <?php printf('%.4f', $be_tot_wt); ?>
        </td>
        <td> Difference:</td><td>
            <?php printf('%.4f', $be_diff); ?>
        </td>
    </tr>

    <tr class="arial10">
        <td>Al carrier: </td>
        <td> Final less initial wt:</td>
        <td><?php printf('%.4f', $al_diff_wt) ?></td>
        <td> Sum of indiv. wts.:</td>
        <td><?php printf('%.4f', $al_tot_wt) ?></td>
        <td> Difference:</td>
        <td><?php printf('%.4f', $al_diff) ?></td>
    </tr>
</table>

<table width="800">
    <tr><td><hr></td></tr>
    <tr>
    <td align="center">
    <input type=submit value="Save and refresh">
    </td>
    </tr>
    <tr><td><hr></td></tr>
</table>
<?php echo form_close(); ?>

<?php $this->load->view('quartz_chem/bottom_links'); ?>

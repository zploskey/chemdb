<?=form_open('quartz_chem/icp_quality_control', '', 
    array('batch_id' => $batch->id, 'refresh' => true)) // hidden vars ?>

<table width=800 class=arial10>
    <tr>
        <td> 
        <h3>Batch information:</p></h3>
        Batch ID: <?=$batch->id?> <br>
        Batch start date: <?=$batch->start_date?> <br>
        Batch owner: <?=$batch->owner?> <br>
        Batch description: <?=$batch->description?> <br>
        </td>
    </tr>
    <tr>
        <td colspan=4>Batch notes:<br>
            <textarea align="center" name="notes" rows="5" cols="100"><?=$batch->notes?></textarea>
        </td>
    </tr>
    <tr><td><hr></td></tr>
</table>

<table width=800 class=arial8>

<tr><td colspan=11><hr></td></tr>
<tr>
    <td>Analysis ID</td>
    <td>Sample name</td>
    <td>Split number</td>
    <td>Split beaker ID</td>
    <td>Run no.</td>
    <td>ICP [Be]</td>
    <td>ug Be</td>
    <td>OK?</td>
    <td>ICP [Al]</td>
    <td>ug Al</td>
    <td>OK?</td>
</tr>

<? for ($a = 0, $an = &$batch->Analysis; $a < $numsamples; $a++): ?>
    
    <tr><td colspan=11><hr></td></tr>
    <tr>
        
        <? for ($s = 0, $sp = &$an->Split, $nsp = $sp->count(); $s < $nsp; $s++): ?>
        
            <? if ($s == 0): ?>
                <td><?=$an[$a]->id?></td>
                <td><?=$an[$a]->sample_name?></td>
            <? else: ?>
                <td colspan="2"></td>
            <? endif; ?>
            
        <!-- // Formerly column 5. Dil factor, split 1
        unset($this_split_1_wt,$this_ICP_1_wt,$this_df_1); -->
        $this_split_1_wt = $this_sample['wt_split_bkr_1_split'] - $this_sample['wt_split_bkr_1_tare'];
        $this_ICP_1_wt = $this_sample['wt_split_bkr_1_ICP'] - $this_sample['wt_split_bkr_1_tare'];
        $this_df_1 = $this_ICP_1_wt / $this_split_1_wt;
        
     <!--<td> <? // printf('%.3f',$this_df_1) ?> </td> -->
        
        <!-- Column 5. Run number -->
            <? for ($r = 0, $run = &$sp->IcpRun, $nruns = $run->count(); $r < $nruns; $r++): ?>
                
                <? if ($r == 0):?>
                    <td>Split <?=$sp->split_num?></td>
                    <td><?=$sp->sample_name?></td>
                <? else: ?>
                    <td colspan="2"></td>
                <? endif; ?>
                
                <td>Run <?=$r->run_num?></td>
                <td><? printf('%.4f', $r->be_result); ?></td>
                
            $this_tot_Be_b1_r1 = $this_df_1 * $this_sample['ICP_Be_split1_run1'] * $this_soln_wt;
                
                <td><? printf('%.1f', $tot_be[$a][$s][$r]); ?></td>
                <td>
                    <?php 
                    echo '<input type="checkbox" value="y" name="use_be[]" ';
                    if ($use_be[$a][$s][$r] == 'y') {
                        print "checked"; 
                    }
                    echo '>'; ?>
                </td>
                <td><? printf('%.4f', $r->al_result); ?></td>
                
                $this_tot_Al_b1_r1 = $this_df_1 * $this_sample['ICP_Al_split1_run1'] * $this_soln_wt;
                
                <td><? printf('%.1f', $tot_al[$a][$s][$r]); ?></td>
                <td>
                    <input type=checkbox value="y" name="use_al[]" 
                        <? if ($use_be[$a][$s][$r] == 'y') { echo "checked"; } ?> >
                </td>
            </tr>
            <? endfor; // runs?>
            
        <? endfor; // splits ?>
        
        <!-- Row 6: Averages -->
        <tr>
            <td colspan=5></td>
            <td>Average ug:</td>
            <td colspan="2">
                <? printf('%.1f', $be_avg[$a]); ?>
                +/-
                <?printf('%.1f', $be_sd[$a]); ?>
            </td>
            <td></td>

            <td colspan=2>
                <? printf('%.1f', $al_avg[$a]); ?>
                +/- 
                <? printf('%.1f', $al_sd[$a]); ?>
            </td>
        </tr>

        <!-- ROW 7. PERCENT UNCERTAINTY -->

        <tr>
            <td colspan=5></td>
            <td>Percent error:</td>
            <td colspan=2><? printf('%.1f', $pct_err_be[$a]); ?></td>
            <td></td>
            <td colspan=2><? printf('%.1f', $pct_err_al[$a]); ?></td>
        </tr>

        <tr>
            <td colspan=5></td>
            <td>Pct. recovery:</td>
            <td colspan=2><? printf('%.1f', $recovery_be[$a]); ?></td>
            <td></td>
            <td colspan=2><? printf('%.1f', $recovery_al[$a]); ?></td>
        </tr>

        <? if ( ($a % 2) == 0): ?>
            <tr><td colspan=11><hr></td></tr>
            <tr>
                <td colspan=11 align=center>
                    <input type="submit" value="Save and refresh">
                </td>
            </tr>
        <? endif; ?>
        
<? endfor; // analyses ?>
        

<tr><td colspan=11><hr></td></tr>

</table>

<?=form_close()?>

<?=form_open('quartz_chem/index')?>
    <div align="center">
        <input type="submit" value="I'm done -- back to main menu">
    </div>  
<?=form_close()?>

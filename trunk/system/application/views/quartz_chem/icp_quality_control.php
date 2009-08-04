<?=form_open('quartz_chem/icp_quality_control', '', 
    array('batch_id' => $batch['id'], 'refresh' => true)) // hidden vars ?>

<table width="800" class="arial10">
    <?=$this->load->view('tr_main_link_hr')?>
    <tr>
        <td> 
        <h3>Batch information:</p></h3>
        Batch ID: <?=$batch['id']?> <br>
        Batch start date: <?=$batch['start_date']?> <br>
        Batch owner: <?=$batch['owner']?> <br>
        Batch description: <?=$batch['description']?> <br>
        </td>
    </tr>
    <tr>
        <td colspan="4">Batch notes:<br>
            <textarea align="center" name="notes" rows="5" cols="100"><?=$batch['notes']?></textarea>
        </td>
    </tr>
    <tr><td><hr></td></tr>
</table>

<?php if ($errors) echo validation_errors(); ?>

<table width="800" class="arial8">
<?php 
// save the column labels for repeated printing within the analysis loop
echo $cols = <<<COL
<tr><td colspan="11"><hr></td></tr>
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
COL;

for ($a = 0; $a < $batch['nsamples']; $a++):
    $an = &$batch['Analysis'][$a]; ?>
    <tr><td colspan="11"><hr></td></tr>
    <tr>    
    <? for ($s = 0; $s < $an['nsplits']; $s++): ?>
    
        <? if ($s == 0): ?>
            <td><?=$an['id']?></td>
            <td><?=$an['sample_name']?></td>
        <? else: ?>
            <td colspan="2"></td>
        <? endif; ?>
        
        <? for ($r = 0; $r < $an['Split'][$s]['nruns']; $r++): // ICP run loop
            $run = &$an['Split'][$s]['IcpRun'][$r];  ?>
            
            <? if ($r == 0):?>
                <td>Split <?=$an['Split'][$s]['split_num']?></td>
                <td><?=$an['Split'][$s]['SplitBkr']['bkr_number']?></td>
            <? else: ?>
                <td colspan="4"></td>
            <? endif; ?>
            
            <td>Run <?=$run['run_num']?></td>
            <td><? printf('%.4f', $run['be_result']); ?></td>
            <td><? printf('%.1f', $run['be_tot']); ?></td>
            <td>
                <input type="checkbox" value="<?=$run['id']?>" name="use_be[]" <?=($run['use_be']=="y")?'checked':''?>>
            </td>
            <td><? printf('%.4f', $run['al_result']); ?></td>
            <td><? printf('%.1f', $run['al_tot']); ?></td>
            <td>
                <input type="checkbox" value="<?=$run['id']?>" name="use_al[]" <?=($run['use_al']=="y")?'checked':''?>>
            </td>
        </tr>
        <? endfor; // runs?>
            
    <? endfor; // splits ?>
    
    <!-- Row 6: Averages -->
    <tr>
        <td colspan="5"></td>
        <td>Average ug:</td>
        <td colspan="2">
            <? printf('%.1f', $an['be_avg']); ?>
            +/-
            <?printf('%.1f', $an['be_sd']); ?>
        </td>
        <td></td>

        <td colspan="2">
            <? printf('%.1f', $an['al_avg']); ?>
            +/- 
            <? printf('%.1f', $an['al_sd']); ?>
        </td>
    </tr>

    <!-- ROW 7. PERCENT UNCERTAINTY -->

    <tr>
        <td colspan="5"></td>
        <td>Percent error:</td>
        <td colspan="2"><? printf('%.1f', $an['be_pct_err']); ?></td>
        <td></td>
        <td colspan="2"><? printf('%.1f', $an['al_pct_err']); ?></td>
    </tr>

    <tr>
        <td colspan="5"></td>
        <td>Pct. recovery:</td>
        <td colspan="2"><? printf('%.1f', $an['be_recovery']); ?></td>
        <td></td>
        <td colspan="2"><? printf('%.1f', $an['al_recovery']); ?></td>
    </tr>

    <? if ( ($a % 2) != 0): ?>
        <tr><td colspan="11"><hr></td></tr>
        <tr>
            <td colspan="11" align="center">
                <input type="submit" value="Save and refresh">
            </td>
        </tr>
        <? if ($a != $batch['nsamples'] - 1) echo $cols; ?>
    <? endif; ?>
    
<? endfor; // analyses ?>

<tr><td colspan="11"><hr></td></tr>

</table>

<?=form_close()?>

<?=form_open('quartz_chem/index')?>
    <div align="center">
        <input type="submit" value="I'm done -- back to main menu">
    </div>  
<?=form_close()?>
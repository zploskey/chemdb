<?=$this->load->view('quartz_chem/intermediate_report')?>

<table width="800" class="arial8">
    <tr>
        <td colspan="12" class="arial10">
            <b>Al determination:</b>    
        </td>
    <tr>
    <tr>
        <td></td>
        <td colspan=<?=$batch['max_nsplits'] * $batch['max_nruns']?> align="center">
            Corrected ICP [Al]<br><hr>
        </td>
        <td colspan=<?=$batch['max_nsplits'] * $batch['max_nruns'] * 2?> align="center">
            Total Al resulting (ug)<br><hr>
        <td>
        <td colspan="3"></td>
    <tr align="center">
        <td align="left">
            Sample name
        </td>
        <?
        for ($s = 1; $s <= $batch['max_nsplits']; $s++) {
            for ($r = 1; $r <= $batch['max_nruns']; $r++) {
                echo '<td align="center">Split ' . $s . '<br>Run ' . $r;
            }
        }
        
        for ($s = 1; $s <= $batch['max_nsplits']; $s++) {
            for ($r = 1; $r <= $batch['max_nruns']; $r++) {
                echo '<td align="center">Split ' . $s . '<br>Run ' . $r;
                echo '<td>OK?</td>';
            }
        }
        ?>
        <td colspan="3">
            Total Al (ug)
        </td>
        <td>
            % SD
        </td>
        <td>
            %<br>Recovery
        </td>
    </tr>
    <tr>
        <td colspan="<?=(6 + $batch['max_nsplits'] * $batch['max_nruns'] * 3)?>"><hr></td>
    </td>

<? foreach ($batch['Analysis'] as $a): ?>
    
    <tr align="center">
        
        <td align="left"><?=$a['sample_name']?></td>

        <?php
        for ($s = 0; $s < $batch['max_nsplits']; $s++) {
            if ($s < $a['nsplits']) {
                // we have a split
                for ($r = 0; $r < $batch['max_nruns']; $r++) {
                    if ($r < $a['Split'][$s]['nruns']) {
                        // got a run, print it
                        echo '<td>', 
                             sprintf('%.4f', $a['Split'][$s]['IcpRun'][$r]['al_result']),
                             '</td>';
                    } else {
                        // there aren't as many runs for this split, print a blank
                        echo '<td></td>';
                    }
                }
            } else {
                // no split here, print blanks
                echo '<td></td>';
            }
        }
    
        // total Al arising and y/n's 
        for ($s = 0; $s < $batch['max_nsplits']; $s++) {
            if ($s < $a['nsplits']) {
                // we have a split
                for ($r = 0; $r < $batch['max_nruns']; $r++) {
                    if ($r < $a['Split'][$s]['nruns']) {
                        // got a run, print it
                        echo '<td>', 
                             sprintf('%.4f', $a['Split'][$s]['IcpRun'][$r]['al_tot']),
                             '</td><td>', 
                             $a['Split'][$s]['IcpRun'][$r]['use_al'],
                             '</td>';
                    } else {
                        // there aren't as many runs for this split, print a blank
                        echo '<td colspan="2"></td>';
                    }
                }
            } else {
                // no split here, print blanks
                echo '<td colspan="2"></td>';
            }
        }
        ?>
    
        <td><?=sprintf('%.1f', $a['al_avg'])?></td>
        <td>&plusmn;</td>
        <td><?=sprintf('%.1f', $a['al_sd'])?></td>
    
        <td><?=sprintf('%.1f',$a['al_pct_err'])?></td>
    
        <td><?=sprintf('%.1f', $a['al_recovery'])?></td>
    </tr>
    
<? endforeach; ?>

    <tr><td colspan="<?=(6 + $batch['max_nsplits'] * $batch['max_nruns'] * 3)?>">
    <hr><hr></td>
    </tr>
</table>

<table width="800" class="arial8">
    <tr>
        <td colspan="12" class="arial10">
            <b>Be determination:</b>    
        </td>
    <tr>
    <tr>
        <td></td>
        <td colspan=<?=$batch['max_nsplits'] * $batch['max_nruns']?> align="center">
            Corrected ICP [Be]<br><hr>
        </td>
        <td colspan=<?=$batch['max_nsplits'] * $batch['max_nruns'] * 2?> align="center">
            Total Be resulting (ug)<br><hr>
        <td>
        <td colspan="3"></td>
    <tr align="center">
        <td align="left">
            Sample name
        </td>
        <?
        for ($s = 1; $s <= $batch['max_nsplits']; $s++) {
            for ($r = 1; $r <= $batch['max_nruns']; $r++) {
                echo '<td align="center">Split ' . "$s<br>Run$r";
            }
        }
        
        for ($s = 1; $s <= $batch['max_nsplits']; $s++) {
            for ($r = 1; $r <= $batch['max_nruns']; $r++) {
                echo '<td align="center">Split ' . "$s<br>Run$r";
                echo '<td>OK?</td>';
            }
        }
        ?>
        <td colspan="3">
            Total Be (ug)
        </td>
        <td>
            % SD
        </td>
        <td>
            %<br>Recovery
        </td>
    </tr>
    <tr>
        <td colspan="<?=(6 + $batch['max_nsplits'] * $batch['max_nruns'] * 3)?>"><hr></td>
    </td>

<? foreach ($batch['Analysis'] as $a): ?>

    <tr align="center">
    <td align="left"><?=$a['sample_name']?></td>
    
    <?php
    for ($s = 0; $s < $batch['max_nsplits']; $s++) {
        if ($s < $a['nsplits']) {
            // we have a split
            for ($r = 0; $r < $batch['max_nruns']; $r++) {
                if ($r < $a['Split'][$s]['nruns']) {
                    // got a run, print it
                    echo '<td>', 
                         sprintf('%.4f', $a['Split'][$s]['IcpRun'][$r]['be_result']),
                         '</td>';
                } else {
                    // there aren't as many runs for this split, print a blank
                    echo '<td></td>';
                }
            }
        } else {
            // no split here, print blanks
            echo '<td></td>';
        }
    }
    
    // total Be arising and y/n's 
    for ($s = 0; $s < $batch['max_nsplits']; $s++) {
        if ($s < $a['nsplits']) {
            // we have a split
            for ($r = 0; $r < $batch['max_nruns']; $r++) {
                if ($r < $a['Split'][$s]['nruns']) {
                    // got a run, print it
                    echo '<td>', 
                         sprintf('%.4f', $a['Split'][$s]['IcpRun'][$r]['be_tot']),
                         '</td><td>', 
                         $a['Split'][$s]['IcpRun'][$r]['use_be'],
                         '</td>';
                } else {
                    // there aren't as many runs for this split, print a blank
                    echo '<td colspan="2"></td>';
                }
            }
        } else {
            // no split here, print blanks
            echo '<td colspan="2"></td>';
        }
    }
    ?>
    
    <td><?=sprintf('%.1f', $a['be_avg'])?></td>
    <td>&plusmn;</td>
    <td><?=sprintf('%.1f', $a['be_sd'])?></td>

    <td><?=sprintf('%.1f', $a['be_pct_err'])?></td>

    <td><?=sprintf('%.1f', $a['be_recovery'])?></td>
    
<? endforeach; ?>


<tr><td colspan="<?=(6 + $batch['max_nsplits'] * $batch['max_nruns'] * 3)?>"><hr><hr></td></tr>

</table>
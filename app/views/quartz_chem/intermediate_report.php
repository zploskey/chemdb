<table width="800" class="arial10">
    <tr>
        <td colspan="3">
            <h3><?=$title?></h3>
        </td>
    </tr>
    <tr>
        <td colspan="3">
        <h4>Batch information:</h4>
        </td>
    </tr>
    <tr>
        <td>
            Batch ID: <?=$batch['id']?>
        </td>
        <td>
            Batch owner: <?=$batch['owner']?>
        </td>
        <td>
            Logged in as: <?=htmlentities($_SERVER['REMOTE_USER'])?>
        </td>
    </tr>
    <tr>
        <td>
            Batch start date: <?=$batch['start_date']?>
        </td>
        <td>
            ICP date:  <?=$batch['icp_date']?>
        </td>
        <td>
            Today's date: <?=$todays_date?>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <p>Batch description: <?=$batch['description']?></p>
            <p>Batch notes: <?=$batch['notes']?></p>

            <?php if (isset($final) && $final): ?>
                <p>Spreadsheet: <?=$batch['spreadsheet_name']?></p>
                <p>CSV file: <?=$batch['csv_name']?></p>
            <?php endif; ?>

        </td>
    </tr>
    <tr><td colspan="3"><hr><hr></td></tr>
</table>

<table width="800" class="arial8">
    <tr>
        <td class="arial10" colspan="12">
            <b>Carrier information:</b><br><br>
        </td>
    </tr>
    <tr align="center">
        <td align="left">Be carrier</td>
        <td>[Be]</td>
        <td>Initial wt.</td>
        <td>Final wt.</td>
        <td>Wt. difference</td>
        <td>Wt. dispensed</td>
        <td align="left">Al carrier</td>
        <td>[Al]</td>
        <td>Initial wt.</td>
        <td>Final wt.</td>
        <td>Wt. difference</td>
        <td>Wt. dispensed</td>
    </tr>
    <tr>
        <td colspan="12"><hr></td>
    </tr>
    <tr align="center">
        <td align="left">
            <?=$batch['BeCarrier']['name']?>
        </td>
        <td>
            <?php echo $batch['BeCarrier']['be_conc'], ' &plusmn; ', $batch['BeCarrier']['del_be_conc'];?>
        </td>
        <td>
            <?=$batch['wt_be_carrier_init']?>
        </td>
        <td>
            <?=$batch['wt_be_carrier_final']?>
        </td>
        <td>
            <?=$batch['wt_be_carrier_diff']?>
        </td>
        <td>
            <?=$batch['wt_be_carrier_disp']?>
        </td>
        <td align="left">
            <?=$batch['AlCarrier']['name']?>
        </td>
        <td>
            <?php echo $batch['AlCarrier']['al_conc'], ' &plusmn; ', $batch['AlCarrier']['del_al_conc'];?>
        </td>
        <td>
            <?=$batch['wt_al_carrier_init']?>
        </td>
        <td>
            <?=$batch['wt_al_carrier_final']?>
        </td>
        <td>
            <?=$batch['wt_al_carrier_diff']?>
        </td>
        <td>
            <?=$batch['wt_al_carrier_disp']?>
        </td>
    </tr>
    <tr>
        <td colspan="12"><hr><hr></td>
    </tr>
</table>

<table width="800" class="arial8">
    <tr>
        <td colspan="12" class="arial10">
            <b>Weights directly measured:</b>
        </td>
    </tr>
    <tr align="center">
        <td align="left">
            Analysis ID
        </td>
        <td align="left">
            Sample name
        </td>
        <td align="center">
            Diss<br>bottle
        </td>
        <td align="center">
            Bottle<br>tare wt.
        </td>
        <td align="center">
            Bottle + <br> sample wt.
        </td>
        <td align="center">
            Bottle + <br> HF sol'n wt.
        </td>
        <td align="center">
            Be carrier<br>sol'n wt.
        </td>
        <td align="center">
            Al carrier<br>sol'n wt.
        </td>
        <td align="center">
            Split<br>bkr
        </td>
        <td align="center">
            Split bkr<br>tare
        </td>
        <td align="center">
            Split bkr<br>+ split
        </td>
        <td align="center">
            Split bkr<br>+ ICP sol'n
        </td>
    </tr>
    <tr>
        <td colspan="12"><hr></td>
    </tr>

<?php foreach ($batch['Analysis'] as $a): ?>

    <tr align="center">
        <td align="left"><?=$a['id']?></td>
        <td align="left"><?=$a['sample_name']?></td>
        <td><?=$a['DissBottle']['bottle_number']?></td>
        <td><?=$a['wt_diss_bottle_tare']?></td>
        <td><?=$a['wt_diss_bottle_sample']?></td>
        <td><?=$a['wt_diss_bottle_total']?></td>
        <td><?=$a['wt_be_carrier']?></td>
        <td><?=$a['wt_al_carrier']?></td>
    <?
    $first = true;
    foreach ($a['Split'] as $s) {
        if ($first == false) {
            echo '<tr align="center"><td colspan="8"></td>';
        } else {
            $first = false;
        }
        echo '<td>',
                (isset($s['SplitBkr'])) ? $s['SplitBkr']['bkr_number'] : '',
            '</td>',
            '<td>', $s['wt_split_bkr_tare'], '</td>',
            '<td>', $s['wt_split_bkr_split'], '</td>',
            '<td>', $s['wt_split_bkr_icp'], '</td></tr>';
    }

    if ($first) echo '</tr>';
    ?>

<?php endforeach; ?>

    <tr><td colspan="12"><hr><hr></td></tr>
</table>

<table width="800" class="arial8">
    <tr>
        <td colspan="12" class="arial10">
            <b>Derived weights: </b>
        </td>
    </tr>
    <tr align="center">
        <td align="left">
            Sample name
        </td>
        <td align="center">
            Sample wt.
        </td>
        <td align="center">
            Total HF<br>sol'n wt.
        </td>
        <td align="center">
            Be carrier wt.
        </td>
        <td align="center">
            Al wt.<br>from carrier
        </td>
        <td align="center">
            Al wt. total<br>per Al check
        </td>
        <? for ($s = 1; $s <= $batch['max_nsplits']; $s++): ?>
            <td align="center">
                Split <?=$s?><br>split wt.
            </td>
        <? endfor; ?>

        <? for ($s = 1; $s <= $batch['max_nsplits']; $s++): ?>
            <td align="center">
                Split <?=$s?><br>ICP wt.
            </td>
        <? endfor; ?>

        <? for ($s = 1; $s <= $batch['max_nsplits']; $s++): ?>
            <td>
                Split <?=$s?><br>multiplier
            </td>
        <? endfor; ?>
    </tr>
    <tr>
        <td colspan="<?php echo 6 + 3 * $batch['max_nsplits']?>"><hr></td>
    </tr>

<?
for ($i = 0; $i < $batch['nsamples']; $i++):
    $a = $batch['Analysis'][$i];
?>

    <tr align="center">
        <td align="left"><?=$a['sample_name']?></td>
        <td><? printf('%.4f', $a['wt_sample']); ?></td>
        <td><? printf('%.4f', $a['wt_HF_soln']); ?></td>
        <td><? printf('%.1f', $a['wt_be']); ?></td>
        <td><? printf('%.1f', $a['wt_al_fromc']); ?></td>
        <td><? printf('%.1f', $a['check_tot_al']); ?></td>
    <?php
    for ($s = 0; $s < $batch['max_nsplits']; $s++) {
        if ($s < $a['nsplits']) {
            echo '<td>',
                 sprintf('%.4f', $a['Split'][$s]['wt_split']),
                 '</td>';
        } else {
            echo '<td></td>';
        }
    }

    for ($s = 0; $s < $batch['max_nsplits']; $s++) {
        if ($s < $a['nsplits']) {
            echo '<td>',
                 sprintf('%.4f', $a['Split'][$s]['wt_icp']),
                 '</td>';
        } else {
            echo '<td></td>';
        }
    }

    for ($s = 0; $s < $batch['max_nsplits']; $s++) {
        if ($s < $a['nsplits']) {
            echo '<td>',
                 sprintf('%.1f', $a['Split'][$s]['tot_df']),
                 '</td>';
        } else {
            echo '<td></td>';
        }
    } ?>
    </tr>

<? endfor; ?>

    <tr><td colspan="<?php echo 6 + 3 * $batch['max_nsplits']?>"><hr><hr></td></tr>
</table>
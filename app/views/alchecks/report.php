<html>
<head>
<title>UW-CNL-DB -- Report Al check results</title>
<base href="<?php echo base_url(); ?>" />

<style>
<!--table {}
.xl24
    {font-family:Arial;
    font-size:10.0pt;}
.xl25
    {font-family:Arial;
    text-align:center;}
.xl26
    {font-family:Arial;
    text-align:right;}
.xl27
    {font-family:Arial;
    text-align:right;}
.xl28
    {font-family:Arial;
    font-size:10.0pt;
    background-color:#FFFFFF}
.xl29
    {font-family:Arial;
    font-size:8.0pt;
    background-color:#EEEEEE}
.red
    {font-family:Arial;
    font-size:8.0pt;
    background-color:#FFA0A0}
.yellow
    {font-family:Arial;
    font-size:8.0pt;
    background-color:#FFFFC8}
.green
    {font-family:Arial;
    font-size:8.0pt;
    background-color:#78FF78}

-->
</style>


</head>

<BODY>


<table width="800">
    <tr><td colspan="2"><hr></td></tr>
    <tr>
        <td class="xl24" valign=middle>
            <h2>Al check report</h2></p>

            Batch ID: <?php echo $batch->id; ?><br/>
            Batch date: <?php echo $batch->prep_date; ?><br/>
            ICP date: <?php echo $batch->icp_date; ?><br>
            Batch owner: <?php echo $batch->owner; ?><br/>
            Batch description: <?php echo $batch->description; ?><br/>
            Number of samples: <?php echo $nsamples; ?><br/>
            Today's date: <?php echo date('Y-m-d'); ?><br/>
            Logged in as: <?php echo $user; ?>

        </td>
        <td align="right"><img src="img/logo.jpeg"></p></td>
    </tr>
    <tr><td colspan="2"><hr></td></tr>
</table>

<table width="800" class="xl29" cellpadding=0>


<thead>
    <tr>
        <td >DB ID</td>
        <td align="left" width="120" class="xl29">Sample name</td>
        <td align="center" >Bkr. ID</td>
        <td align="center" class="xl29">Sample wt.</td>

        <td width="10"></td>

        <td align="center" width="40" >ICP<br>[Be]</td>
        <td align="center" width="40" >ICP<br>[Mg]</td>
        <td align="center" width="40" >ICP<br>[Al]</td>
        <td align="center" width="40" >ICP<br>[Ca]</td>
        <td align="center" width="40" >ICP<br>[Ti]</td>
        <td align="center" width="40" >ICP<br>[Fe]</td>

        <td width="10"></td>

        <td align="right" width="30" class="xl29">Qtz<br>[Be]</td>
        <td align="right" width="30" class="xl29">Qtz<br>[Mg]</td>
        <td align="right" width="30" class="xl29">Qtz<br>[Al]</td>
        <td align="right" width="30" class="xl29">Qtz<br>[Ca]</td>
        <td align="right" width="30" class="xl29">Qtz<br>[Ti]</td>
        <td align="right" width="30" class="xl29">Qtz<br>[Fe]</td>
        <td width="10"></td>
        <td width="50" align="left">Notes</td>
    </tr>
</thead>
    <tr><td colspan=18></p><hr></p></td></tr>
<tbody>

<?php
for ($a = 0; $a < $nsamples; $a++):
    $an = $batch['AlcheckAnalysis'][$a];
?>

    <tr>
        <td><?php echo $an['id']; ?></td>
        <td class=<?php echo $color[$a]; ?>><?php echo $sample_name[$a]; ?></td>
        <td align="center"><?php echo $an['bkr_number']; ?></td>
        <td align="center" class="xl29"><?php echo sprintf('%.4f', $sample_wt[$a]); ?></td>
        <td></td>
        <td align="center" class="xl29"><?php echo sprintf('%.3f', $an['icp_be']); ?></td>
        <td align="center" class="xl29"><?php echo sprintf('%.3f', $an['icp_mg']); ?></td>
        <td align="center" class="xl29"><?php echo sprintf('%.3f', $an['icp_al']); ?></td>
        <td align="center" class="xl29"><?php echo sprintf('%.3f', $an['icp_ca']); ?></td>
        <td align="center" class="xl29"><?php echo sprintf('%.3f', $an['icp_ti']); ?></td>
        <td align="center" class="xl29"><?php echo sprintf('%.3f', $an['icp_fe']); ?></td>
        <td></td>
        <td align="right" class=<?php echo $color[$a]; ?>><?php echo sprintf('%.1f', $qtz_be[$a]); ?></td>
        <td align="right" class=<?php echo $color[$a]; ?>><?php echo sprintf('%.1f', $qtz_mg[$a]); ?></td>
        <td align="right" class=<?php echo $color[$a]; ?>><?php echo sprintf('%.1f', $qtz_al[$a]); ?></td>
        <td align="right" class=<?php echo $color[$a]; ?>><?php echo sprintf('%.1f', $qtz_ca[$a]); ?></td>
        <td align="right" class=<?php echo $color[$a]; ?>><?php echo sprintf('%.1f', $qtz_ti[$a]); ?></td>
        <td align="right" class=<?php echo $color[$a]; ?>><?php echo sprintf('%.1f', $qtz_fe[$a]); ?></td>
        <td></td>
        <td align="left"><?php echo $an['notes']; ?></td>
    </tr>

<?php endfor; ?>

</tbody>
<tr><td colspan=18><hr></td></tr>
</table>

</body>
</html>

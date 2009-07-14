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


<table width=800>
    <tr><td colspan=2><hr></td></tr>
    <tr>
        <td class=xl24 valign=middle>
            <h2>Al check report</h2></p>

            Batch ID: <?=$batch->id?><br/>
            Batch date: <?=$batch->prep_date?><br/>
            ICP date: <?=$batch->icp_date?><br>
            Batch owner: <?=$batch->owner?><br/>
            Batch description: <?=$batch->description?><br/>
            Number of samples: <?=$nsamples?><br/>
            Today's date: <?=date('Y-m-d')?><br/>
            Logged in as: <?=htmlentities($_SERVER['REMOTE_USER'])?>

        </td>
        <td align=right><img src="img/logo.jpeg"></p></td>
    </tr>
    <tr><td colspan=2><hr></td></tr>
</table>

<table width=800 class=xl29 cellpadding=0>
	

<thead>
    <tr>
    	<td >DB ID</td>
    	<td align=left width=120 class=xl29>Sample name</td>
    	<td align=center >Bkr. ID</td>
    	<td align=center class=xl29>Sample wt.</td>
    	<td width=10></td>
    	<td align=center width=40 >ICP<br>[Be]</td>
    	<td align=center width=40 >ICP<br>[Ti]</td>
    	<td align=center width=40 >ICP<br>[Fe]</td>
    	<td align=center width=40 >ICP<br>[Al]</td>
    	<td align=center width=40 >ICP<br>[Mg]</td>
    	<td width=10></td>
    	<td align=right width=30 class=xl29>Qtz<br>[Be]</td>
    	<td align=right width=30 class=xl29>Qtz<br>[Ti]</td>
    	<td align=right width=30 class=xl29>Qtz<br>[Fe]</td>
    	<td align=right width=30 class=xl29>Qtz<br>[Al]</td>
    	<td align=right width=30 class=xl29>Qtz<br>[Mg]</td>
    	<td width=10></td>
    	<td width=50 align=left>Notes</td>
	</tr>
</thead>
    <tr><td colspan=18></p><hr></p></td></tr>
<tbody>

<? for ($a = 0; $a < $nsamples; $a++): 
    $an = $batch['AlcheckAnalysis'][$a];
?>
 
	<tr>	
    	<td><?=$an['id']?></td>	
    	<td class=<?=$color[$a]?>><?=$sample_name[$a]?></td>
    	<td align=center><?=$an['bkr_number']?></td>	
    	<td align=center class=xl29><?=sprintf('%.4f', $sample_wt[$a])?></td>
    	<td></td>
    	<td align=center class=xl29><?=sprintf('%.3f', $an['icp_be'])?></td>	
    	<td align=center class=xl29><?=sprintf('%.3f', $an['icp_ti'])?></td>
    	<td align=center class=xl29><?=sprintf('%.3f', $an['icp_fe'])?></td>
    	<td align=center class=xl29><?=sprintf('%.3f', $an['icp_al'])?></td>
    	<td align=center class=xl29><?=sprintf('%.3f', $an['icp_mg'])?></td>
    	<td></td>
    	<td align=right class=<?=$color[$a]?>><?=sprintf('%.1f', $qtz_be[$a])?></td>
    	<td align=right class=<?=$color[$a]?>><?=sprintf('%.1f', $qtz_ti[$a])?></td>
    	<td align=right class=<?=$color[$a]?>><?=sprintf('%.1f', $qtz_fe[$a])?></td>
    	<td align=right class=<?=$color[$a]?>><?=sprintf('%.1f', $qtz_al[$a])?></td>
    	<td align=right class=<?=$color[$a]?>><?=sprintf('%.1f', $qtz_mg[$a])?></td>
    	<td></td>	
    	<td align=left><?=$an['notes']?></td>
	</tr>
	
<? endfor; ?>

</tbody>
<tr><td colspan=18><hr></td></tr>
</table>

</body>
</html>
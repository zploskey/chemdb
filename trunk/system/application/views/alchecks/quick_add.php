<?=form_open('alchecks/quick_add')?>

<input type=hidden name=refresh value="yes">

<? if ($batch_id): // a batch has already been created, keep track of its id ?>

    <input type=hidden name=batch_id value="<?=$batch->id?>">
    
<? endif; ?>


<table width=800 class=arial10>

<tr>
    <td>Sample name: <input type=text
    		size=20
    		name=sample_name value="<?=$analysis->sample_name?>">
    </td>
		
    <td>[Al] (ppm in qtz): <input type=text
    		size=7
    		name=icp_al value="<?=$analysis->icp_al?>">
    </td>

    <td>[Fe] (ppm in qtz): <input type=text
    		size=7
    		name=icp_fe value="<?=$analysis->icp_fe?>">
    </td>
		
    <td>[Ti] (ppm in qtz): <input type=text
    		size=7
    		name=icp_ti value="<?=$analysis->icp_ti?>">
    </td>		
</tr>

<tr><td colspan=4><hr>
    <?
    if ($errors) {
        echo validation_errors() . '<hr>';
    } 
    ?>
    </td></tr>



<tr>
    <td colspan=4 align=center>
        <input type=submit value="Save and refresh">
    </td>
</tr>

</table>

<?=form_close()?>

<?=form_open('alchecks/quick_add')?>

<table width=800><tr><td><hr></td></tr>
    <tr>
    	<td align=center>
    	<input type=submit value="Done with this one -- create a new dummy Al check">
    	</td>
    </tr>
    <tr><td><hr></td></tr>
</table>

<?=form_close()?>
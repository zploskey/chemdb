<table width="800">
    <?php echo form_open(site_url('quartz_chem/intermediate_report'),
        array('target' => '_blank'),
        array('batch_id' => $batch->id)) ?>
        <tr>
            <td align="center">
                <input type=submit value="Print hardcopy backup of weights in a new window">
            </td>
        </tr>
        <tr><td><hr></td></tr>
    <?php echo form_close(); ?>

    <?php echo form_open(site_url('quartz_chem/index')); ?>
        <tr>
            <td align="center">
            <input type=submit value="I'm done -- back to main menu">
            </td>
        </tr>
        <tr><td><hr></td></tr>
    <?php echo form_close(); ?>
</table>

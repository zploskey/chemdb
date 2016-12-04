<?php $this->load->view('containers/nav'); ?>
<p>
    <h2>
        <?php echo $subtitle; ?>
        (<?php echo anchor("containers/edit/$type/" . $container->id, 'Edit'); ?>)
    </h2>
</p>
<br>
<div class="data">
    <table>
        <tr>
            <td align="right">Number: &nbsp;</td>
            <td><?php echo $number; ?></td>
        </tr>
    </table>
</div>
<br/>

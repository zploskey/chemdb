<?php $this->load->view('containers/nav'); ?>
<p>
    <h2>
        <?php
        echo $subtitle;

        if ($container->id) {
            echo ' ('
                .anchor("containers/view/$type/$container->id", 'View')
                .')';
        }
        ?>
    </h2>
</p>
<br>

<?php echo form_open(site_url("containers/edit/$type/$container->id")); ?>

    <input type="hidden" name="is_refresh" value="TRUE">
    <div class="formblock">
        <label>ID:</label>
        <?php echo $container->id; ?>
    </div>
    <div class="formblock">
        <label>Number:</label>
        <input type="text" name="<?php echo $namefield; ?>" value="<?php echo $container->$namefield; ?>"/>
    </div>
    <br>
    <input type="submit" value="Submit" />
    <br><br><?php echo validation_errors(); ?><br>

<?php echo form_close(); ?>

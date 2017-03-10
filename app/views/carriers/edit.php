<?php $this->load->view('carriers/nav'); ?>
<p>
    <h2>
        <?php
        echo $subtitle;

        if ($carrier->id) {
            echo ' ('
                . anchor("carriers/view/$element/$carrier->id", 'View')
                . ')';
        }
        ?>
    </h2>
</p>
<br/>

<?php echo form_open(site_url("carriers/edit/$element/$carrier->id")); ?>

    <input type="hidden" name="is_refresh" value="TRUE">
    <div class="formblock">
        <label>Name</label>
        <input type="text" name="carrier[name]" value="<?php echo $carrier->name; ?>"/>
    </div>
    <div class="formblock">
        <label>Al Concentration (ppm)</label>
        <input type="text" name="carrier[al_conc]" value="<?php echo (float)$carrier->al_conc; ?>"/>
        &plusmn;
        <input type="text" name="carrier[del_al_conc]" value="<?php echo (float)$carrier->del_al_conc; ?>"/>
    </div>
<?php if ($element === 'be'): ?>
    <div class="formblock">
        <label>Be Concentration (ppm)</label>
        <input type="text" name="carrier[be_conc]" value="<?php echo (float)$carrier->be_conc; ?>"/>
        &plusmn;
        <input type="text" name="carrier[del_be_conc]" value="<?php echo (float)$carrier->del_be_conc; ?>"/>
    </div>
    <div class="formblock">
        <label>Be-10/Be-9 Ratio</label>
        <input type="text" name="carrier[r10to9]" value="<?php echo (float)$carrier->r10to9; ?>"/>
        &plusmn;
        <input type="text" name="carrier[r10to9_error]" value="<?php echo (float)$carrier->r10to9_error; ?>"/>
    </div>
<?php elseif ($element === 'al'): ?>
    <div class="formblock">
        <label>Al-26/Al-27 Ratio</label>
        <input type="text" name="carrier[r26to27]" value="<?php echo (float)$carrier->r26to27; ?>"/>
        &plusmn;
        <input type="text" name="carrier[r26to27_error]" value="<?php echo (float)$carrier->r26to27_error; ?>"/>
    </div>
<?php endif ?>
    <div class="formblock">
        <label>In-service Date (YYYY&#8209;MM&#8209;DD)</label>
        <input type="text" name="carrier[in_service_date]" value="<?php echo $carrier->in_service_date; ?>"/>
    </div>
    <div class="formblock">
        <label>Manufacturer Lot No.</label>
        <input type="text" name="carrier[mfg_lot_no]" value="<?php echo $carrier->mfg_lot_no; ?>"/>
    </div>
    <div class="formblock">
        <label>Owner</label>
        <input type="text" name="carrier[owner]" value="<?php echo $carrier->owner; ?>"/>
    </div>
    <div class="formblock">
        <label>Notes</label>
        <input type="text" name="carrier[notes]" value="<?php echo $carrier->notes; ?>"/>
    </div>
    <div class="formblock">
        <label>In use?</label>
        <input type="checkbox" name="carrier[in_use]" value="1" <?php echo $in_use ? 'checked' : ''; ?>/>
    </div>
    <br/>
    <input type="submit" value="Submit" />
    <br/><br/><?php echo validation_errors(); ?><br/>

<?php echo form_close(); ?>

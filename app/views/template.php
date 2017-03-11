<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">

<html>
    <head>
        <title>UW-CNL-DB -- <?php echo $title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <base href="<?php echo base_url(); ?>" />
        <?php echo link_tag('css/style.css'); ?>
        <?php echo link_tag('js/autocomplete/jquery.autocomplete.css'); ?>
        <?php echo link_tag('js/autocomplete/lib/thickbox.css'); ?>
        <script type="text/javascript">
        //<![CDATA[
        base_url = '<?php echo base_url(); ?>';
        //]]>
        </script>
        <script type="text/javascript" src="vendor/components/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="js/autocomplete/lib/jquery.bgiframe.min.js"></script>
        <script type='text/javascript' src="js/autocomplete/lib/jquery.ajaxQueue.js"></script>
        <script type='text/javascript' src="js/autocomplete/lib/thickbox-compressed.js"></script>
        <script type="text/javascript" src="js/autocomplete/jquery.autocomplete.js"></script>
        <?php
        if (isset($extraHeadContent)) {
            echo $extraHeadContent;
        }
        ?>
    </head>

    <body>
        <div id="wrapper">
            <div id="header">
                <?php $this->load->view('header'); ?>
            </div>
            <div id="nav">
                <?php $this->load->view('navigation'); ?>
            </div>
            <div id="main">
                <?php $this->load->view($main); ?>
            </div>
            <div id="footer">
                <?php $this->load->view('footer'); ?>
            </div>
        </div>
    </body>
</html>

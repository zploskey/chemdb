<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">

<html>
    <head>
        <title>UW-CNL-DB -- <?php echo $title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <base href="<?php echo base_url(); ?>" />
        <?php echo link_tag('vendor/components/normalize.css/normalize.css'); ?>
        <?php echo link_tag('vendor/components/jqueryui/themes/base/jquery-ui.css'); ?>
        <?php echo link_tag('css/style.css'); ?>
        <script type="text/javascript">
            var base_url = "<?php echo base_url(); ?>";
        </script>
        <script type="text/javascript" src="vendor/components/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="vendor/components/jqueryui/jquery-ui.min.js"></script>
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

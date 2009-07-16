<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">

<html>
    <head>
        <title>UW-CNL-DB -- <?=$title?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <base href="<?php echo base_url(); ?>" />
        <?php echo link_tag('css/style.css'); ?>
    </head>

    <body>
        <div id="wrapper">
            <div id="header">
                <?=$this->load->view('header')?>
            </div>
            <div id="nav">
                <?=$this->load->view('navigation')?>
            </div>
            <div id="main">
                <?=$this->load->view($main)?>
            </div>
            <div id="footer">
                <?=$this->load->view('footer')?>
            </div>
        </div>
    </body>
</html>
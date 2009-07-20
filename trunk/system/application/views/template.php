<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">

<html>
    <head>
        <title>UW-CNL-DB -- <?=$title?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <base href="<?=base_url()?>" />
        <?=link_tag('css/style.css')?>
        <?=link_tag('js/autocomplete/jquery.autocomplete.css')?>
        <?=link_tag('js/autocomplete/lib/thickbox.css')?>
        <script type="text/javascript">
        //<![CDATA[
        base_url = '<?=base_url();?>';
        //]]>
        </script>
        <script type="text/javascript" src="js/jquery-1.3.1.min.js"></script>
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
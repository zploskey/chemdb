<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
		<title>UW-CNL-DB -- <?=$title?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<base href="<?php echo base_url(); ?>" />
		<style>
        table {}
        .arial10
        	{font-family:Arial;
        	font-size:10.0pt;}
        .arial8
        	{font-family:Arial;
        	font-size:8.0pt;}
        .arial14
        	{font-family:Arial;
        	font-size:14.0pt;}
        .arial12
        	{font-family:Arial;
        	font-size:12.0pt;}
        </style>
	</head>

	<body>
		<div id="main">
			<?=$this->load->view($main)?>
		</div>
	</body>
	
</html>
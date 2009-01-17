<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>UW-CNL-DB -- <?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<base href="<?=base_url()?>" />
	<?=link_tag('css/style.css')?>
</head>

<body id="template">
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
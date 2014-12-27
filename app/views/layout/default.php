<!DOCTYPE html>
<?php $this->page = !empty($this->page) ? "page--".$this->page.' ' : ''; ?>
<!--[if lt IE 7]>      <html lang="en" class="<?php echo $this->page ?>no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="en" class="<?php echo $this->page ?>no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="en" class="<?php echo $this->page ?>no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" class="<?php echo $this->page ?>no-js "> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php echo !empty($this->title) ? $this->title : APP_TITLE  ?></title>
		<meta name="description" content="<?php echo !empty($this->description) ? $this->description : APP_TITLE ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- dev : /css/style.css -->
		<?php $this->css = PROD ? '/css/style.min.css?v=957465612021901' : '/css/style.css?v='.time() ?>
		<link rel="stylesheet" href="<?php echo $this->css ?>">
		<script src="/js/modernizr.custom.js"></script>
	</head>
	<body>

		<!--[if lte IE 8]>
			<p class="obsolete-browser">You use an <strong>obsolete</strong> browser. <a href="http://browsehappy.com/" target="_blank">Update it</a> to navigate <strong>safely</strong> on the Internet!</p>
		<![endif]-->

		<!-- dev : /js/script.js -->
		<?php $this->js = PROD ? '/js/script.min.js?v=58797562341' :
			['/bower_components/jquery/dist/jquery.js',
			'/js/script.js'];
		$t = time();
		if (is_string($this->js)): ?>
		<script src="<?php echo $this->js ?>"></script>
		<?php else: foreach ($this->js as $script): ?>
		<script src="<?php echo $script ?>?v=<?php echo $t ?>"></script>
		<?php endforeach; endif; ?>
	</body>
</html>
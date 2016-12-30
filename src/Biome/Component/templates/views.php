<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<title><?php echo $view->getTitle() ?></title>
	<link href="<?php echo URL::getAsset('css/vendor.min.css') ?>" rel="stylesheet">
	<?php
		if(file_exists(PUBLIC_DIR . '/css/app.min.css'))
		{
			?><link href="<?php echo URL::getAsset('css/app.min.css') ?>" rel="stylesheet"><?php
		}

		$css_list = $this->getCss();
		foreach($css_list AS $css_file)
		{
			?><link href="<?php echo URL::getAsset('css/' . $css_file) ?>" rel="stylesheet"><?php
		}
	?>
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
	<div id="wrapper">
		<?php echo $this->getContent(); ?>
	</div>
	<script src="<?php echo URL::getAsset('js/vendor.min.js') ?>"></script>
	<script src="<?php echo URL::getAsset('js/app.min.js') ?>"></script>
    <script type="text/javascript">
		<?php echo $this->view->printJavascript(); ?>
    </script>
</body>
</html>

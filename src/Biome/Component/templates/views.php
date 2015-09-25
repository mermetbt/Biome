<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<title><?php echo $view->getTitle() ?></title>
	<link href="<?php echo URL::getAsset('packages/bootstrap/dist/css/bootstrap.min.css') ?>" rel="stylesheet">
	<style>
	body {
		padding-top: 50px;
	}
	</style>
</head>
<body>
	<?php echo $content; ?>
	<script src="<?php echo URL::getAsset('packages/jquery/dist/jquery.min.js') ?>"></script>
	<script src="<?php echo URL::getAsset('packages/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
</body>
</html>

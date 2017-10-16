<?php
$type = $this->getType();
if(!empty($type))
{
	$this->addClasses('navbar-' . $type);
}

if($this->isFixedTop())
{
	$this->addClasses('navbar-fixed-top');
}

$container_class = 'container';
if($this->isExpanded())
{
	$container_class = 'container-fluid';
}
?>
<nav class="navbar <?php echo $this->getClasses(); ?>" role="navigation">
	<div class="<?php echo $container_class; ?>">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-ex1-collapse" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo URL::fromRoute(); ?>"><?php

			$logo = $this->getLogo();
			if(!empty($logo))
			{
				echo '<img src="', URL::getAsset($logo), '" width="100%" height="100%"/>';
			}
			else
			{
				echo $view->getTitle();
			}

			?></a>
		</div>

		<?php echo $this->getContent(); ?>

	</div>
</nav>

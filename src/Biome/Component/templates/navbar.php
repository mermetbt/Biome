<nav class="navbar navbar-default navbar-fixed-top <?php echo $this->getClasses(); ?>" role="navigation">
	<div class="container-fluid">
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

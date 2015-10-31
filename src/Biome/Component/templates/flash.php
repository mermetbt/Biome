<?php

$flash = $this->getFlash();

$hasMessages = $flash->hasMessages();

if($hasMessages)
{
	?><div class="container"><?php
}

foreach($flash->getErrors() AS $title => $msg)
{
	?><div class="alert alert-danger" role="alert"><?php
		?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><?php
		?><span aria-hidden="true">&times;</span></button><?php

		?><i class="glyphicon glyphicon-remove"></i> <?php
		?><strong><?php echo $title; ?></strong><?php
		echo ' ', $msg;
	?></div><?php
}

foreach($flash->getWarnings() AS $title => $msg)
{
	?><div class="alert alert-warning" role="alert"><?php
		?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><?php
		?><span aria-hidden="true">&times;</span></button><?php

		?><i class="fa fa-exclamation-triangle "></i> <?php
		?><strong><?php echo $title; ?></strong><?php
		echo ' ', $msg;
	?></div><?php
}

foreach($flash->getInfos() AS $title => $msg)
{
	?><div class="alert alert-info" role="alert"><?php
		?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><?php
		?><span aria-hidden="true">&times;</span></button><?php

		?><i class="fa fa-info-circle"></i> <?php
		?><strong><?php echo $title; ?></strong><?php
		echo ' ', $msg;
	?></div><?php
}

foreach($flash->getSuccess() AS $title => $msg)
{
	?><div class="alert alert-success" role="alert"><?php
		?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><?php
		?><span aria-hidden="true">&times;</span></button><?php

		?><i class="glyphicon glyphicon-ok"></i> <?php
		?><strong><?php echo $title; ?></strong><?php
		echo ' ', $msg;
	?></div><?php
}

if($hasMessages)
{
	?></div><?php
}

$flash->flushMessages();

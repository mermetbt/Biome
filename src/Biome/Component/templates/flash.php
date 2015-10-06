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

		?><h4><?php echo $title; ?></h4><?php
		?><p><?php echo $msg; ?></p><?php
	?></div><?php
}

foreach($flash->getWarnings() AS $title => $msg)
{
	?><div class="alert alert-warning" role="alert"><?php
		?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><?php
		?><span aria-hidden="true">&times;</span></button><?php

		?><h4><?php echo $title; ?></h4><?php
		?><p><?php echo $msg; ?></p><?php
	?></div><?php
}

foreach($flash->getInfos() AS $title => $msg)
{
	?><div class="alert alert-info" role="alert"><?php
		?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><?php
		?><span aria-hidden="true">&times;</span></button><?php

		?><h4><?php echo $title; ?></h4><?php
		?><p><?php echo $msg; ?></p><?php
	?></div><?php
}

foreach($flash->getSuccess() AS $title => $msg)
{
	?><div class="alert alert-success" role="alert"><?php
		?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><?php
		?><span aria-hidden="true">&times;</span></button><?php

		?><h4><?php echo $title; ?></h4><?php
		?><p><?php echo $msg; ?></p><?php
	?></div><?php
}

if($hasMessages)
{
	?></div><?php
}

$flash->flushMessages();

<?php

$src = $this->getSrc();
$alt = $this->getAlt();

if(empty($src))
{
	return;
}

if(empty($alt))
{
	$alt = $src;
}

?><img class="<?php echo $this->getClasses(); ?>" src="<?php echo $src; ?>" alt="<?php echo $alt; ?>"/><?php

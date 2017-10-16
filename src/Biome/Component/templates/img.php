<?php

$src = $this->getSrc();
$alt = $this->getAlt();
$width = $this->getWidth();
$height = $this->getHeight();

if(empty($src))
{
	return;
}

if(empty($alt))
{
	$alt = $src;
}

$dimension = '';
if(!empty($width))
{
	$dimension .= ' width=""';
}

if(!empty($height))
{
	$dimension .= ' height=""';
}

?><img class="<?php echo $this->getClasses(); ?>" src="<?php echo $src; ?>" alt="<?php echo $alt; ?>"<?php echo $dimension; ?>/><?php

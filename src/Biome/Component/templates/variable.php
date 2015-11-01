<?php

$field = $this->getField();

$rights = \Biome\Biome::getService('rights');

$viewable = $field === NULL || $rights->isAttributeView($field);

if($viewable)
{
	echo $this->getValue();
}
else
{
	?><i class="fa fa-ban"></i><?php
}

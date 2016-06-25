<?php

$field = $this->getField();
$value = $this->getValue();

$rights = \Biome\Biome::getService('rights');

$viewable = $field === NULL || $rights->isAttributeView($field);

if($viewable)
{
	if($field !== NULL && $this->getType() == 'enum')
	{
		$enumeration = $field->getEnumeration();
		if(isset($enumeration[$value]))
		{
			echo $enumeration[$value];
		}
	}
	else
	{
		echo $value;
	}
}
else
{
	?><i class="fa fa-ban"></i><?php
}

<?php

namespace Biome\Core\ORM;

abstract class AbstractField
{
	protected $name;
	protected $title;
	protected $default_value = '';

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setDefaultValue($value)
	{
		$this->default_value = $value;
	}

	public function getDefaultValue()
	{
		return $this->default_value;
	}

}

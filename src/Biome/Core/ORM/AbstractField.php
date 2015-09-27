<?php

namespace Biome\Core\ORM;

abstract class AbstractField
{
	protected $name;
	protected $title;
	protected $default_value = NULL;

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getType()
	{
		$class_name = get_class($this);
		$raw = explode('\\', $class_name);
		$inner_class = end($raw);
		$type = substr($inner_class, 0, -strlen('Field'));
		return strtolower($type);
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

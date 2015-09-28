<?php

namespace Biome\Core\ORM;

abstract class AbstractField
{
	protected $name;
	protected $label;
	protected $default_value = NULL;

	public static function create()
	{
		$class_name = get_called_class();
		return new $class_name(func_get_args());
	}

	public function getType()
	{
		$class_name = get_class($this);
		$raw = explode('\\', $class_name);
		$inner_class = end($raw);
		$type = substr($inner_class, 0, -strlen('Field'));
		return strtolower($type);
	}

	/**
	 * Default Accessors
	 */

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setDefaultValue($value)
	{
		$this->default_value = $value;
		return $this;
	}

	public function getDefaultValue()
	{
		return $this->default_value;
	}

	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}

	public function getLabel()
	{
		return $this->label;
	}
}

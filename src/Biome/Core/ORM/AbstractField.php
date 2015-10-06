<?php

namespace Biome\Core\ORM;

abstract class AbstractField
{
	protected $name;
	protected $label;
	protected $default_value = NULL;
	protected $required = FALSE;

	protected $_error_list = array();

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

	public function isReady()
	{
		return TRUE;
	}

	public function applyGet($value)
	{
		return $value;
	}

	public function applySet($value)
	{
		return $value;
	}

	public function setError($type, $message)
	{
		$this->_error_list[$type] = $message;
		return TRUE;
	}

	public function hasErrors()
	{
		return !empty($this->_error_list);
	}

	public function getErrors()
	{
		$list = $this->_error_list;
		$this->_error_list = array();
		return $list;
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

	public function setRequired($required = TRUE)
	{
		$this->required = TRUE;
		return $this;
	}

	public function isRequired()
	{
		return $this->required === TRUE;
	}
}

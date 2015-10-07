<?php

namespace Biome\Core\ORM;

use Biome\Core\ORM\Converter\ConverterInterface;

abstract class AbstractField
{
	protected $name;
	protected $label;
	protected $default_value = NULL;
	protected $required = FALSE;

	protected $_error_list = array();

	protected $_apply_converter = array();

	public static function create()
	{
		$reflector = new \ReflectionClass(get_called_class());
		return $reflector->newInstanceArgs(func_get_args());
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

	/**
	 * Formatter
	 */
	public function applyGet($value)
	{
		foreach($this->_apply_converter AS $converter)
		{
			$value = $converter->get($value);
		}

		return $value;
	}

	public function applySet($value)
	{
		foreach($this->_apply_converter AS $converter)
		{
			$value = $converter->set($value);
		}

		return $value;
	}

	public function setConverter(ConverterInterface $converter)
	{
		$class_name = get_class($converter);
		$this->_apply_converter[$class_name] = $converter;
		return $this;
	}

	/**
	 * Errors handling (validation).
	 */
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

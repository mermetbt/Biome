<?php

namespace Biome\Core\ORM;

use Biome\Core\ORM\Converter\ConverterInterface;
use Biome\Core\ORM\Models;

abstract class AbstractField
{
	private $name;
	private $model_name;
	protected $label;
	protected $default_value = NULL;
	protected $required = FALSE;
	protected $editable = TRUE;

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

	public function validate($object, $field_name)
	{
		if(!$object->hasFieldValueChanged($field_name))
		{
			return TRUE;
		}

		$value = $object->$field_name;

		if(!$this->isRequired() && empty($value))
		{
			return TRUE;
		}

		if(empty($value))
		{
			$this->setError('required', 'Field "' . $this->getLabel() . '" is required!');
			return FALSE;
		}

		return TRUE;
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

	public function setModel($model_name)
	{
		$this->model_name = $model_name;
		return $this;
	}

	public function getModel()
	{
		return $this->model_name;
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
		$this->required = $required == TRUE;
		return $this;
	}

	public function isRequired()
	{
		return $this->required === TRUE;
	}

	public function isEditable()
	{
		return $this->editable;
	}

	public function setEditable($editable = TRUE)
	{
		$this->editable = $editable == TRUE;
		return $this;
	}
}

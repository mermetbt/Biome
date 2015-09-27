<?php

namespace Biome\Core\ORM;

abstract class Models implements ObjectInterface
{
	protected $_structure;
	protected $_values;

	public function __set($field_name, $value)
	{
		if($value instanceof AbstractField)
		{
			return $this->setStructure($field_name, $value);
		}

		return $this->setValue($field_name, $value);
	}

	public function __get($field_name)
	{
		return $this->getValue($field_name);
	}

	public function getFieldType($field_name)
	{
		return $this->_structure[$field_name]->getType();
	}

	protected function setStructure($field_name, AbstractField $field)
	{
		$field->setName($field_name);
		$this->_structure[$field_name] = $field;
		$this->_values['old'][$field_name] = '';
		$this->_values['new'][$field_name] = $field->getDefaultValue();
		return TRUE;
	}

	protected function setValue($attribute, $value)
	{
		if(!isset($this->_structure[$attribute]))
		{
			throw new \Exception('Undefined field ' . $attribute . ' in object ' . get_class($this) . '!');
		}
		$this->_values['new'][$attribute] = $value;
		return TRUE;
	}

	protected function getValue($attribute)
	{
		if(isset($this->_values['new'][$attribute]))
		{
			return $this->_values['new'][$attribute];
		}
		else
		if(isset($this->_values['old'][$attribute]))
		{
			return $this->_values['old'][$attribute];
		}
		else
		if(isset($this->_structure[$attribute]))
		{
			return $this->_structure[$attribute]->getDefaultValue();
		}
		else
		{
			throw new \Exception('Undefined field ' . $attribute . ' in object ' . get_class($this) . '!');
		}
	}
}

<?php

namespace Biome\Core\ORM;

abstract class Models implements ObjectInterface
{
	protected $_structure;
	protected $_values;

	private $_query_set = NULL;

	public function __construct($values = array(), QuerySet $qs = NULL)
	{
		$this->_query_set = $qs;

		$this->fields();

		foreach($values AS $attribute => $value)
		{
			if(!isset($this->_structure[$attribute]))
			{
				continue;
				//throw new \Exception('Undefined field ' . $attribute . ' in object ' . get_class($this) . '!');
			}
			$this->_values['old'][$attribute] = $value;
		}
	}

	public function __set($field_name, $value)
	{
		if($value instanceof AbstractField)
		{
			return $this->setField($field_name, $value);
		}

		return $this->setValue($field_name, $value);
	}

	public function __get($field_name)
	{
		return $this->getValue($field_name);
	}

	/**
	 * Operations over fields.
	 */
	public function setField($field_name, AbstractField $field)
	{
		$field->setName($field_name);
		$this->_structure[$field_name] = $field;
		return TRUE;
	}

	public function getField($field_name)
	{
		return $this->_structure[$field_name];
	}

	public function hasField($field_name)
	{
		return isset($this->_structure[$field_name]);
	}

	/**
	 * Operations over values.
	 */
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
			// Fetch attribute if object has an ID.
			if($this->getId() != NULL)
			{
				$this->_query_set->fields($attribute)->fetch();

				return $this->getValue($attribute);
			}
			else
			{
				// Otherwise return the default value.
				return $this->_structure[$attribute]->getDefaultValue();
			}
		}
		else
		{
			throw new \Exception('Undefined field ' . $attribute . ' in object ' . get_class($this) . '!');
		}
	}

	public function getId()
	{
		$id = $this->parameters()['primary_key'];
		if(isset($this->$id))
		{
			return $this->$id;
		}
		return NULL;
	}

	/**
	 *
	 * Action over models.
	 *
	 */

	/**
	 * Return a QuerySet for this object.
	 */
	public static function all()
	{
		return (new QuerySet(get_called_class()));
	}

	/**
	 * Get an object from an ID.
	 */
	public static function get($id)
	{
		return self::all()->get($id);
	}

	/**
	 * Get a collection of object from a condition
	 */
	public static function filter($where)
	{
		return self::all()->filter($where);
	}
}

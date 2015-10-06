<?php

namespace Biome\Core\ORM;

use Biome\Core\ORM\Field\PrimaryField;

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
		if(!isset($this->_structure[$field_name]))
		{
			throw new \Exception('Undefined field ' . $field_name . ' in object ' . get_class($this) . '!');
		}
		return $this->_structure[$field_name];
	}

	public function hasField($field_name)
	{
		if(!is_string($field_name))
		{
			throw new \Exception('hasField must have a string in parameter! Found: ' . print_r($field_name, true));
		}
		return isset($this->_structure[$field_name]);
	}

	/**
	 * Operations over values.
	 */
	protected function setValue($attribute, $value)
	{
		$f = $this->getField($attribute);

		$new = $f->applySet($value);
		if(!isset($this->_values['old'][$attribute]) || $new !== $this->_values['old'][$attribute])
		{
			$this->_values['new'][$attribute] = $new;
		}
		return TRUE;
	}

	protected function getValue($attribute)
	{
		$f = $this->getField($attribute);

		if(isset($this->_values['new'][$attribute]))
		{
			return $f->applyGet($this->_values['new'][$attribute]);
		}

		if(isset($this->_values['old'][$attribute]))
		{
			return $f->applyGet($this->_values['old'][$attribute]);
		}

		// Fetch attribute if object has an ID.
		$pk = $this->parameters()['primary_key'];
		if(isset($this->$pk))
		{
			$this->_query_set->fields($attribute)->fetch();

			return $this->getValue($attribute);
		}
		else
		{
			// Otherwise return the default value.
			return $f->getDefaultValue();
		}
	}

	/**
	 * Return the identifier of the current object.
	 */
	public function getId()
	{
		$pk = $this->parameters()['primary_key'];
		$id = $this->$pk;
		if(!empty($id))
		{
			return $id;
		}
		return NULL;
	}

	private function setId($id)
	{
		$pk = $this->parameters()['primary_key'];
		$this->_values['old'][$pk] = $id;
		return TRUE;
	}

	/**
	 * Retrieve the object in the database.
	 */
	public function sync()
	{
		$id = $this->getId();
		if($id === NULL)
		{
			return $this;
		}

		$obj = $this->_query_set->get($id);

		// Reset object.
		unset($this->_values['new']);
		unset($this->_values['old']);

		foreach($this->_structure AS $f_name => $f_object)
		{
			$f = $obj->getField($f_name);
			if($f->isReady())
			{
				$this->_values['old'][$f_name] = $obj->$f_name;
			}
		}
		return $this;
	}

	/**
	 * Fetch the object in the database with the specific fields.
	 * If NULL is set, the object will be retrieve with all the
	 * fields without thoses corresponding to the primary keys.
	 */
	public function fetch(...$fields)
	{
		if($this->getId() !== NULL)
		{
			return $this;
		}

		$filters = array();
		if(!empty($fields))
		{
			foreach($fields AS $f)
			{
				$filters[] = array($f, '=', $this->$f);
			}
		}
		else
		{
			$pks = $this->parameters()['primary_key'];
			if(is_string($pks))
			{
				$pks = array($pks => $pks);
			}

			foreach($this->_structure AS $f)
			{
				if($f instanceof PrimaryField)
				{
					continue;
				}

				if(isset($pks[$f]))
				{
					continue;
				}

				$filters[] = array($f, '=', $this->$f);
			}
		}

		$result = self::all()->filter($filters);
		$count = count($result);
		if($count > 1)
		{
			throw new \Exception('Too many results!');
		}

		if($count == 0)
		{
			return NULL;
		}

		$obj = $result->current();

		if(!$obj instanceof Models)
		{
			throw new \Exception('Internal Error: The QuerySet doesn\'t return an object of instance Models! ' . print_r($obj, true));
		}

		foreach($this->_structure AS $f_name => $field)
		{
			$f = $obj->getField($f_name);

			// Reset the field
			unset($this->_values['old'][$f_name]);

			// Set the value from the database
			if($f->isReady())
			{
				$this->_values['old'][$f_name] = $obj->$f_name;
			}
		}

		return $this;
	}

	public function detach()
	{
		$this->setId(NULL);
		return $this;
	}

	/**
	 * Insert or update the object in the database.
	 */
	public function save()
	{
		if($this->_query_set === NULL)
		{
			$this->_query_set = self::all();
		}

		$id = $this->getId();

		// Creation
		if($id === NULL)
		{
			$this->_query_set->create($this->_values['new'], $id);
			$this->setId($id);
			$this->sync();
		}
		else
		if(!empty($this->_values['new']))
		{
			$this->_query_set->update($id, $this->_values['new']);
			$this->sync();
		}

		return TRUE;
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

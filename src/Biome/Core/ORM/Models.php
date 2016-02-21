<?php

namespace Biome\Core\ORM;

use Biome\Core\ORM\Field\PrimaryField;
use Biome\Core\ORM\Field\Many2OneField;
use Biome\Core\ORM\Field\Many2ManyField;
use Biome\Core\ORM\Field\One2ManyField;
use Biome\Core\ORM\Inspector\ModelInspectorInterface;

abstract class Models implements ObjectInterface
{
	protected $_structure;
	protected $_values;

	private $_query_set = NULL;

	public function __construct($values = array(), QuerySet $qs = NULL)
	{
		$this->_query_set = $qs;

		/**
		 * Fields.
		 */
		$this->fields();

		/**
		 * Set the values.
		 */
		foreach($values AS $attribute => $value)
		{
			if(!isset($this->_structure[$attribute]))
			{
				//continue;
				throw new \Exception('Undefined field ' . $attribute . ' in object ' . get_class($this) . '!');
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
		$field->setModel(get_called_class());

		// TODO: Find a way to make a clean management of field.
		if($field instanceof Many2OneField)
		{
			$field_name_local = substr($field_name, 0, -3);
			$f = clone $field;
			$f->setName($field_name_local);
			$this->_structure[$field_name_local] = $f;
		}

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

	public function getFieldsName()
	{
		return array_keys($this->_structure);
	}

	/**
	 * Operations over values.
	 */
	protected function setValue($attribute, $value)
	{
		$f = $this->getField($attribute);

		if($f instanceof Many2OneField && !($value instanceof Models) && substr($attribute, -3) != '_id')
		{
			throw new \Exception('Unable to store a Models in a non Many2OneField: ' . get_class($value));
		}

		$new = $f->applySet($value);
		if(!isset($this->_values['old'][$attribute]) ||
			$new !== $this->_values['old'][$attribute] ||
			(isset($this->_values['new'][$attribute]) && $new !== $this->_values['new'][$attribute]))
		{
			$this->_values['new'][$attribute] = $new;

			if($f instanceof Many2OneField && $new instanceof Models)
			{
				if(substr($attribute, -3) != '_id')
				{
					$this->_values['new'][$attribute . '_id'] = $new->getId();
				}
			}
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
			if($this->_values['old'][$attribute] instanceof RawSQL)
			{
				return NULL;
			}
			return $f->applyGet($this->_values['old'][$attribute]);
		}

		// Fetch attribute if object has an ID.
		$pks = $this->parameters()['primary_key'];
		if(is_array($pks))
		{
			$all_pk_set = TRUE;
			foreach($pks AS $pk)
			{
				$all_pk_set = $all_pk_set && isset($this->$pk);
			}
		}
		else
		{
			$all_pk_set = isset($this->$pks);
		}

		if($all_pk_set)
		{
			$this->_query_set->fields($attribute)->fetch();

			return $this->getValue($attribute);
		}

		if($f instanceof Many2ManyField)
		{
			if($this->_query_set === NULL)
			{
				$this->_query_set = self::all();
				if(is_array($pks))
				{
					foreach($pks AS $pk)
					{
						$this->_query_set->filter($pk, '=', $this->getId($pk));
					}
				}
				else
				{
					$this->_query_set->filter($pks, '=', $this->getId());
				}
			}
			$qs = $f->generateQuerySet($this->_query_set, $attribute);
			$this->_values['old'][$attribute] = $qs;
			return $qs;
		}

		// Otherwise return the default value.
		$default = $f->getDefaultValue();
		if($default instanceof RawSQL)
		{
			return NULL;
		}

		return $default;

	}

	public function getRawValue($attribute)
	{
		$f = $this->getField($attribute);

		if(isset($this->_values['new'][$attribute]))
		{
			return $this->_values['new'][$attribute];
		}

		if(isset($this->_values['old'][$attribute]))
		{
			return $this->_values['old'][$attribute];
		}

		// Fetch attribute if object has an ID.
		$pks = $this->parameters()['primary_key'];
		if(is_array($pks))
		{
			$all_pk_set = TRUE;
			foreach($pks AS $pk)
			{
				$all_pk_set = $all_pk_set && isset($this->$pk);
			}
		}
		else
		{
			$all_pk_set = isset($this->$pks);
		}

		if($all_pk_set)
		{
			$this->_query_set->fields($attribute)->fetch();

			return $this->getRawValue($attribute);
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
	public function getId($field = NULL)
	{
		$pks = $this->parameters()['primary_key'];
		if(is_array($pks))
		{
			if($field != NULL)
			{
				if(!in_array($field, $pks))
				{
					return NULL;
				}

				if(empty($this->_values['old'][$field]))
				{
					return NULL;
				}

				$id = $this->_values['old'][$field];

				return $id;
			}

			$id = array();
			foreach($pks AS $pk)
			{
				if(!empty($this->_values['old'][$pk]))
				{
					$id[] = $this->_values['old'][$pk];
				}
			}

			if(empty($id))
			{
				return NULL;
			}
		}
		else
		{
			if(empty($this->_values['old'][$pks]))
			{
				return NULL;
			}

			$id = $this->_values['old'][$pks];
		}

		return $id;
	}

	private function setId($id)
	{
		$pks = $this->parameters()['primary_key'];
		if(is_array($pks))
		{
			foreach($pks AS $index => $pk)
			{
				$this->_values['old'][$pk] = $id[$index];
			}
			return TRUE;
		}

		$this->_values['old'][$pks] = $id;
		return TRUE;
	}

	public function hasChanges()
	{
		return isset($this->_values['new']);
	}

	/**
	 * Retrieve the object in the database.
	 */
	public function sync($id = NULL)
	{
		if($id === NULL)
		{
			$id = $this->getId();
			if($id === NULL)
			{
				return $this;
			}
		}

		$obj = self::all()->get($id);

		if($obj === FALSE)
		{
			throw new \Exception('Object ' . get_called_class() . ' not found! id=' . print_r($id, true));
		}

		if(!$obj instanceof Models)
		{
			throw new \Exception('Internal Error: The QuerySet doesn\'t return an object of instance Models! ' . print_r($obj, true));
		}

		// Reset object.
		unset($this->_values['new']);
		unset($this->_values['old']);

		foreach($this->_structure AS $f_name => $f_object)
		{
			$f = $obj->getField($f_name);
			if($f->isReady())
			{
				$this->_values['old'][$f_name] = $obj->getRawValue($f_name);
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
		if($this->getId() != NULL)
		{
			return $this;
		}

		$result = self::all();
		if(!empty($fields))
		{
			foreach($fields AS $f)
			{
				$result->filter($f, '=', $this->getRawValue($f));
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

				$result->filter($f, '=', $this->getRawValue($f));
			}
		}

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
				$this->_values['old'][$f_name] = $obj->getRawValue($f_name);
			}
		}

		return $this;
	}

	/**
	 * Detach this element from the database.
	 */
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

		/* Check required fields */
		if(!$this->validate())
		{
			return FALSE;
		}

		$data = isset($this->_values['new']) ? $this->_values['new'] : array();

		// Create and clean Many2One elements.
		foreach($this->_structure AS $field_name => $field)
		{
			if(!$field instanceof Many2OneField)
			{
				continue;
			}

			if(substr($field_name, -3) === '_id')
			{
				continue;
			}

			if(!isset($data[$field_name]))
			{
				continue;
			}

			$value = $data[$field_name];

			if(!empty($value))
			{
				if(!$value instanceof Models)
				{
					throw new \Exception('Value in a Many2OneField is not an object of type Models!');
				}

				if($value->getId() === NULL)
				{
					if(!$value->save())
					{
						throw new \Exception('Unable to save the object for the field ' . $field_name . '!');
					}
				}

				$data[$field_name . '_id'] = $value->getId();
			}

			unset($data[$field_name]);
		}

		// Creation
		if($id === NULL)
		{
			$this->_query_set->create($data, $id);
			$this->setId($id);
		}
		else
		if($this->hasChanges())
		{
			$this->_query_set->update($id, $data);
		}

		// (Dis)Associate Many2Many and One2Many elements.
		foreach($this->_structure AS $field_name => $field)
		{
			if(!$field instanceof Many2ManyField)
			{
				continue;
			}

			$v = $this->getValue($field_name);
			if($v->hasChanges())
			{
				$field->operateChanges($this, $v);
			}
		}

		// Final sync to retrieve the values saved.
		$this->sync();

		return TRUE;
	}

	/**
	 * Delete this elements in the database.
	 */
	public function delete()
	{
		$id = $this->getId();
		if($id === NULL)
		{
			return FALSE;
		}

		self::all()->delete($id);

		return TRUE;
	}

	/**
	 * Execute the validators on this object.
	 */
	public function validate(...$fields)
	{
		$errors = FALSE;
		foreach($this->_structure AS $field_name => $field)
		{
			if(!empty($fields) && !in_array($field_name, $fields))
			{
				continue;
			}

			if(!$field->isRequired())
			{
				continue;
			}

			if($field instanceof Many2OneField)
			{
				$value = $this->getRawValue($field_name);
				if(!empty($value))
				{
					continue;
				}

				/**
				 * If this field contains the object, check the value.
				 */
				if(substr($field_name, -3) != '_id')
				{
					/* Check the value. */
					$value = $this->getRawValue($field_name . '_id');
					if(!empty($value))
					{
						continue;
					}
				}
				else
				/**
				 * If this field contains the value, check the object.
				 */
				if(substr($field_name, -3) == '_id')
				{
					$value = $this->getRawValue(substr($field_name, 0, -3));
					if(!empty($value))
					{
						continue;
					}
				}
			}

			$value = $this->getRawValue($field_name);

			if(empty($value) && empty($field->getDefaultValue()))
			{
				$field->setError('required', 'Field "' . $field->getLabel() . '" is required!');
				$errors = TRUE;
			}
		}

		return !$errors;
	}

	public function getErrors()
	{
		$errors = array();
		foreach($this->_structure AS $f_name => $field)
		{
			if($field->hasErrors())
			{
				foreach($field->getErrors() AS $title => $error)
				{
					$errors[] = $error;
				}
			}
		}
		return $errors;
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
		$object = self::all()->get($id);
		if(empty($object))
		{
			throw new \Exception('Object ' . get_called_class() . ' of id ' . $id . ' not found!');
		}
		return $object;
	}

	/**
	 * Get a collection of object from a condition
	 */
	public static function find($where)
	{
		return call_user_func_array([self::all(), 'filter'], func_get_args());
	}

	/**
	 * Search objects from a string.
	 */
	public static function searchFromString($string)
	{
		$query_set = self::all();

		$parameters = $query_set->object()->parameters();

		$search_fields = array();

		if(array_key_exists('search', $parameters))
		{
			$search_fields = is_array($parameters['search']) ? $parameters['search'] : array($parameters['search']);
		}
		else
		if(array_key_exists('reference', $parameters))
		{
			$search_fields = array($parameters['reference']);
		}

		foreach($search_fields AS $field)
		{
			$query_set->db()->orWhere($field, 'like', $string . '%');
		}

		return $query_set;
	}

	/**
	 * Print the reference of the object (usually in many2one cases.)
	 */
	public function __toString()
	{
		$parameters = $this->parameters();
		if(array_key_exists('reference', $parameters))
		{
			$reference = $parameters['reference'];
			$str = $this->$reference;
			if(empty($str))
			{
				return '';
			}
			return $this->$reference;
		}

		$str = 'Model ' . get_class($this);

		$str .= ' [<br/>';

		foreach($this->_structure AS $f_name => $f)
		{
			$old = isset($this->_values['old'][$f_name]) ? $this->_values['old'][$f_name] : '';
			$new = isset($this->_values['new'][$f_name]) ? $this->_values['new'][$f_name] : '';

			$old = is_string($old) ? $old : '(NAS)';
			$new = is_string($new) ? $new : '(NAS)';

			$str .= $f_name . " => " . $old . " - " . $new . '<br/>';
		}
		$str .= ']';

		return $str;
	}

	public function inspectModel(ModelInspectorInterface $inspector)
	{
		$inspector->handleParameters($this->parameters());
		foreach($this->_structure AS $field)
		{
			$inspector->handleField($field);
		}
	}
}

<?php

namespace Biome\Core\ORM;

use Iterator, Countable, ArrayAccess;

class QuerySet implements Iterator, Countable, ArrayAccess
{
	/**
	 * Object related property.
	 */
	protected $object_name = '';
	protected $object = NULL;

	/**
	 * QuerySet parameters.
	 */
	protected $fields	= NULL;
	protected $filters	= array();
	protected $orders	= array();
	protected $offset	= NULL;
	protected $limit	= NULL;

	/**
	 * QuerySet runtime attribute.
	 */
	protected $_query_builder	= NULL;
	protected $_db_handler		= NULL;
	protected $_data_set		= array();
	protected $_operations		= array();

	protected $total_count		= 0;

	public function __construct($object_name)
	{
		$this->object_name	= $object_name;
		$this->_db_handler = new Handler\MySQLHandler($this);
	}

	protected function db()
	{
		if($this->_query_builder === NULL)
		{
			$param = $this->object()->parameters();
			$this->_query_builder = new QueryBuilder(isset($param['database']) ? $param['database'] : NULL, $param['table']);
		}
		return $this->_query_builder;
	}

	protected function object()
	{
		if($this->object === NULL)
		{
			$this->object		= ObjectLoader::get($this->object_name, array(), $this);
		}
		return $this->object;
	}

	/**
	 * QuerySet edition.
	 */
	public function hasChanges()
	{
		return count($this->_operations) > 0;
	}

	public function modifiers()
	{
		return $this->_operations;
	}

	/**
	 * Return a copy of the current QuerySet.
	 */
	public function all()
	{
		return clone $this;
	}

	/**
	 * Restrict fields to fetch.
	 */
	public function fields($fields = ['*'])
	{
		$fields = is_array($fields) ? $fields : func_get_args();

		$this->fields = array();
		foreach($fields AS $field_name)
		{
			if($this->object()->hasField($field_name))
			{
				$this->fields[] = $field_name;
			}
			else
			{
				throw new \Exception('Fetching an unexisting field ('.$field_name.') for object ' . $this->object_name . '!');
			}
		}

		$this->db()->select($this->fields);

		return $this;
	}

	/**
	 * Aggregation methods.
	 */
	public function distinct(...$fields)
	{
		return $this;
	}

	public function total($field_name)
	{
		return $this;
	}

	public function sum($field_name)
	{
		return $this;
	}

	public function min($field_name)
	{
		return $this;
	}

	public function max($field_name)
	{
		return $this;
	}

	public function avg($field_name)
	{
		return $this;
	}

	/**
	 * Selection methods.
	 *
	 * filter(array(field, operator, value), array(field2, operator2, value2), ...)
	 * filter(field, operator, value)
	 */
	public function filter($filters = array())
	{
		$filters = is_array($filters) ? func_get_args() : array(func_get_args());

		/* Array of filters. */
		foreach($filters AS $filter)
		{
			if(!is_array($filter))
			{
				throw new \Exception('Filter must be an array! ' . print_r($filter, true));
			}

			$subset = explode('.', $filter[0]);
			$operator = isset($filter[1]) ? $filter[1] : '=';
			$value = isset($filter[2]) ? $filter[2] : '';

			/**
			 * Field is a part of another object.
			 */
			if(count($subset) > 1)
			{
				$field_name = $subset[0];
			}

			$field_name = $subset[0];
			if(!$this->object()->hasField($field_name))
			{
				throw new \Exception('Filtering on an unexisting field ('.$field_name.') for object ' . $this->object_name . '!');
			}

			$field = $this->object()->getField($field_name);

			/**
			 * Field is a part of the object.
			 */
			if(count($subset) == 1)
			{
				if($field instanceof \Biome\Core\ORM\Field\Many2ManyField)
				{
					/* Link table. */
					$table = $field->getLinkObject()->parameters()['table'];
					$foreign_key = $field->getLinkForeignKey();
					$this->db()->join($table, $foreign_key, '=', $this->object()->parameters()['primary_key']);

					/* Destination table. */
					$table_dst = $field->getDestinationObject()->parameters()['table'];
					$pk_dst = $field->getDestinationObject()->parameters()['primary_key'];
					$foreign_key_dst = $field->getDestinationForeignKey();
					$this->db()->join($table_dst, $pk_dst, '=', [$table, $foreign_key_dst]);

					/* Filtering. */
					$this->db()->where([$table_dst, $foreign_key_dst], $operator, $value);
					continue;
				}
				$this->db()->where($field_name, $operator, $value);
				continue;
			}

			/**
			 * Field is a part of another object.
			 */
			if($field instanceof \Biome\Core\ORM\Field\Many2OneField)
			{
				if(substr($field_name, -3) !== '_id')
				{
					$field_name .= '_id';
				}

				$table = $field->object()->parameters()['table'];
				$foreign_key = $field->getForeignKey();
				$this->db()->join($table, $foreign_key, '=', $field_name);
				$this->db()->where([$table, $subset[1]], $operator, $value);
			}
			else
			{
				throw new \Exception('Unsupported filtering!');
			}
		}

		return $this;
	}

	public function order_by($field)
	{
		$this->orders[] = $field;
		$this->db()->orderby($field);
		return $this;
	}

	public function reverse()
	{
		return $this;
	}

	public function limit($offset = 0, $limit = 20)
	{
		$this->db()->offset($offset)->limit($limit);
		$this->offset	= $offset;
		$this->limit	= $limit;
		return $this;
	}

	public function first()
	{
		$this->limit(1, 1);

		$primary_keys = $this->object()->parameters()['primary_key'];
		if(is_array($primary_keys))
		{
			foreach($primary_keys AS $pk)
			{
				$this->order_by($k . ' ASC');
			}
		}
		else
		{
			$this->order_by($primary_keys . ' ASC');
		}
		return $this->current();
	}

	public function last()
	{
		$this->limit(1, 1);

		$primary_keys = $this->object()->parameters()['primary_key'];
		if(is_array($primary_keys))
		{
			foreach($primary_keys AS $pk)
			{
				$this->order_by($k . ' DESC');
			}
		}
		else
		{
			$this->order_by($primary_keys . ' DESC');
		}

		return $this->current();
	}

	/**
	 * QuerySet Operations
	 */
	public function associate($local_attribute_name, QuerySet $query_set, $field_name)
	{
		/* For reading. */
		$l = new LazyFetcher($query_set, $field_name);
		$this->filter($local_attribute_name, 'in', $l);

		/* For writing. */

		return $this;
	}

	public function joinFilter($table, $one, $two, QuerySet $query_set)
	{
		$this->db()->join($table, $one, '=', $two);

		$filters = $query_set->getFilters();

		$this->db()->where($filters);

		return $this;
	}

	public function getFilters()
	{
		return $this->filters;
	}

	/**
	 * Countable interface
	 */
	public function count()
	{
		$this->valid();
		return count($this->_data_set);
	}

	/**
	 *  Iterator interface
	 */

	/* Return the current object. */
	public function current()
	{
		if(!$this->valid())
		{
			return FALSE;
		}
		return current($this->_data_set);
	}

	/* Return the key of the current object. */
	public function key()
	{
		return key($this->_data_set);
	}

	/* Next object. */
	public function next()
	{
		if($this->_data_set == NULL)
		{
			return FALSE;
		}
		return next($this->_data_set);
	}

	/* Restart from the begining. */
	public function rewind()
	{
		if($this->_data_set == NULL)
		{
			return FALSE;
		}
		return reset($this->_data_set);
	}

	/* Return TRUE if some elements are availables. */
	public function valid()
	{
		if(empty($this->_data_set))
		{
			$this->fetch();
		}

		if(empty($this->_data_set))
		{
			return FALSE;
		}

		return current($this->_data_set) != NULL;
	}

	/**
	 * ArrayAccess interface
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_data_set);
	}

	public function offsetGet($offset)
	{
		return $this->_data_set[$offset];
	}

	public function offsetSet($offset, $value)
	{
		/* Check value type (must be the same as the object of this QuerySet. */
		if(!$value instanceof Models)
		{
			throw new \Exception('The value added to the QuerySet must be an instance of Models!');
		}

		if(get_class($value) != $this->object_name)
		{
			throw new \Exception('The value added to the QuerySet must be an instance of ' . $this->object_name . '!');
		}

		/* Get the ID and organize the QuerySet. */
		if(!$value->getId())
		{
			$value->save();
		}
		$offset = $value->getId();

		$this->_data_set[$offset] = $value;

		$this->_operations[$offset] = 'add';
	}

	public function offsetUnset($offset)
	{
		unset($this->_data_set[$offset]);
		if(empty($this->_operations[$offset]))
		{
			$this->_operations[$offset] = 'remove';
		}
		else
		{
			unset($this->_operations[$offset]);
		}
	}

	/**
	 * QuerySet Function
	 */
	public function getTotalCount()
	{
		return $this->total_count;
	}

	/**
	 * Fetch the result from the parameters.
	 */
	public function fetch()
	{
		$object = $this->object_name;
		$query_set = $this;

		$sql = $this->db()->toSql();

		$this->_data_set = $this->_db_handler->processSelect(
			$sql,
			function($row) use($query_set) {
				$object_name	= $query_set->object_name;
				$object			= $query_set->object();

				$fields = $object->getFieldsName();
				foreach($fields AS $field_name)
				{
					$field = $object->getField($field_name);

					if($field instanceof QuerySetFieldInterface)
					{
						if(isset($row[$field_name]))
						{
							$field_name_local = substr($field_name, 0, -3);
							$row[$field_name_local] = $field->getObject($row[$field_name]);
						}
						else
						{
							$field_name_local = $field_name;
							$row[$field_name_local]	= $field->generateQuerySet($query_set, $field_name);
						}
					}
				}

				/* Instanciate object. */
				$o = ObjectLoader::get($object_name, $row, $query_set);
				return $o;
			},
			$this->total_count);

		return $this;
	}

	/**
	 * Get
	 */
	public function get($id)
	{
		$new_qs = clone $this;

		$primary_keys = $this->object()->parameters()['primary_key'];
		if(is_array($primary_keys))
		{
			foreach($primary_keys AS $index => $pk)
			{
				$new_qs->filter($pk, '=', $id[$index]);
			}
		}
		else
		{
			$new_qs->filter($primary_keys, '=', $id);
		}
		return $new_qs->current();
	}

	/**
	 * Creation
	 */
	public function create($data, &$id)
	{
		$id = $this->_db_handler->create(
			$this->object()->parameters(),
			$data
		);
		return $this;
	}

	/**
	 * Update
	 */
	public function update($id, $data)
	{
		$this->_db_handler->update(
			$this->object()->parameters(),
			$id,
			$data
		);

		return $this;
	}

	/**
	 * Delete
	 */
	public function delete($id)
	{
		$this->_db_handler->delete(
			$this->object()->parameters(),
			$id
		);

		return $this;
	}
}

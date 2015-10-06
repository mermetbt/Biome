<?php

namespace Biome\Core\ORM;

use Iterator, Countable;

class QuerySet implements Iterator, Countable
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
	protected $offset	= NULL;
	protected $limit	= NULL;

	/**
	 * QuerySet runtime attribute.
	 */
	protected $_db_handler		= NULL;
	protected $_data_set		= array();

	public function __construct($object_name)
	{
		$this->object_name	= $object_name;
		$this->object		= ObjectLoader::load($object_name);

		$this->_db_handler = new Handler\MySQLHandler($this);
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
	public function fields(...$fields)
	{
		$this->fields = array();
		foreach($fields AS $field_name)
		{
			if($this->object->hasField($field_name))
			{
				$this->fields[] = $field_name;
			}
			else
			{
				throw new \Exception('Fetching an unexisting field ('.$field_name.') for object ' . $this->object_name . '!');
			}
		}
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
	 */
	public function filter(array...$filters_parameters)
	{
		/* Function parameters */
		foreach($filters_parameters AS $filters_group)
		{
			/* Array of filters. */
			foreach($filters_group AS $filter)
			{
				$field_name = $filter[0];
				if(!$this->object->hasField($field_name))
				{
					throw new \Exception('Filtering on an unexisting field ('.$field_name.') for object ' . $this->object_name . '!');
				}

				$this->filters[] = $filter;
			}
		}
		return $this;
	}

	public function order_by($field)
	{
		return $this;
	}

	public function reverse()
	{
		return $this;
	}

	public function limit($offset = 0, $limit = 20)
	{
		$this->offset	= $offset;
		$this->limit	= $limit;
		return $this;
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
	 * Fetch the result from the parameters.
	 */
	public function fetch()
	{
		$object = $this->object_name;
		$query_set = $this;

		$this->_data_set = $this->_db_handler->query(
			$this->object->parameters(),
			$this->fields,
			$this->filters,
			$this->offset,
			$this->limit,
			function($row) use($object, $query_set) {
				$o = new $object($row, $query_set);
				return $o;
		});

		return $this;
	}

	/**
	 * Get
	 */
	public function get($id)
	{
		$filters = array();
		$filters[] = array($this->object->parameters()['primary_key'], '=', $id);
		$object = $this->object_name;
		$query_set = $this;

		$obj = $this->_db_handler->query(
			$this->object->parameters(),
			$this->fields,
			$filters,
			$this->offset,
			$this->limit,
			function($row) use($object, $query_set) {
				$o = new $object($row, $query_set);
				return $o;
		});

		return reset($obj);
	}

	/**
	 * Creation
	 */
	public function create($data, &$id)
	{
		$id = $this->_db_handler->create(
			$this->object->parameters(),
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
			$this->object->parameters(),
			$id,
			$data
		);

		return $this;
	}
}

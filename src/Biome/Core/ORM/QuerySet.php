<?php

namespace Biome\Core\ORM;

use Iterator;

class QuerySet implements Iterator
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

	public function count($field_name)
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
	public function filter(array...$filters)
	{
		foreach($filters AS $filter)
		{
			$this->filters[] = $filter;
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
	 *  Méthodes de l'interface itérateurs.
	 */

	/* Retourne l'objet courant. */
	public function current()
	{
		return current($this->_data_set);
	}

	/* Retourne la clé de l'objet courant. */
	public function key()
	{
		return key($this->_data_set);
	}

	/* Avance d'un objet et retourne l'objet courant. */
	public function next()
	{
		if($this->_data_set == NULL)
		{
			return FALSE;
		}
		next($this->_data_set);
		return TRUE;
	}

	/* Repart du début. */
	public function rewind()
	{
		if($this->_data_set == NULL)
		{
			return FALSE;
		}
		reset($this->_data_set);
		return TRUE;
	}

	/* Retourne TRUE s'il existe un objet courant. */
	public function valid()
	{
		if(empty($this->_data_set))
		{
			$this->fetch();
		}

		if($this->_data_set == NULL)
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

		$this->_data_set = $this->_db_handler->query(
			$this->object->parameters(),
			$this->fields,
			$this->filters,
			$this->offset,
			$this->limit,
			function($row) use($object) {
				$o = new $object($row);
				return $o;
		});

		return $this;
	}
}

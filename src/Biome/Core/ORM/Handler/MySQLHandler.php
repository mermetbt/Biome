<?php

namespace Biome\Core\ORM\Handler;

use Biome\Core\ORM\QuerySet;
use Biome\Biome;

class MySQLHandler
{
	protected $_hDB = NULL;

	public function __construct(QuerySet $qs)
	{
		$this->_hDB = Biome::getService('mysql');
	}

	public function query($parameters, $fields, $filters, $offset, $limit, $objectMapper)
	{
		$query = $this->generateQuery($parameters, $fields, $filters, $offset, $limit);

		$result = $this->_hDB->query($query);

		$data = array();
		while($row = $result->fetch_assoc())
		{
			$o = $objectMapper($row);
			$id = $o->getId();
			if($id == NULL)
			{
				throw new \Exception('No ID for object : ' . print_r($row, true));
			}
			$data[$id] = $o;
		}

		return $data;
	}

	protected function generateSelect($database, $table, $fields)
	{
		$query = 'SELECT ';

		/**
		 * Fields selection
		 */
		if(empty($fields))
		{
			$fields = '*';
		}
		else
		{
			$fields = join(', ', $fields);
		}

		$query .= $fields;

		/**
		 * Database and table
		 */
		$query .= ' FROM `' . $database . '`.`' . $table . '`';

		return $query;
	}

	protected function generateInsert($database, $table, $fields)
	{
		$query = 'INSERT INTO `' . $database . '`.`' . $table . '` SET ';

		$groups = array();
		foreach($fields AS $field_name => $value)
		{
			if($value === NULL)
			{
				continue;
			}
			$groups[] = '`' . $field_name . '`="' . $this->_hDB->real_escape_string($value) . '"';
		}

		$query .= join(', ', $groups);

		return $query;
	}

	protected function generateUpdate($database, $table, $fields)
	{
		$query = 'UPDATE `' . $database . '`.`' . $table . '` SET ';

		$groups = array();
		foreach($fields AS $field_name => $value)
		{
			if($value === NULL)
			{
				continue;
			}
			$groups[] = '`' . $field_name . '`="' . $this->_hDB->real_escape_string($value) . '"';
		}

		$query .= join(', ', $groups);

		return $query;
	}

	protected function generateWhere($table, $filters)
	{
		if(empty($filters))
		{
			return '';
		}

		/**
		 * Restrictions.
		 */
		$where = array();
		foreach($filters AS $filter)
		{
			$column = $this->_hDB->real_escape_string($filter[0]);
			$operator = $this->_hDB->real_escape_string($filter[1]);
			$value = $this->_hDB->real_escape_string($filter[2]);

			$where[] = '`' . $table . '`.`'. $column . '`' . $operator . '"' . $value . '"';
		}

		return ' WHERE ' . join(' AND ', $where);
	}

	protected function generateLimit($offset = NULL, $limit = NULL)
	{
		if($offset === NULL && $limit === NULL)
		{
			return '';
		}

		if($offset === NULL)
		{
			return ' LIMIT ' . $limit;
		}

		if($limit === NULL)
		{
			return '';
		}

		return ' LIMIT ' . $offset . ',' . $limit;
	}

	public function generateQuery($parameters, $fields, $filters, $offset, $limit)
	{
		$database	= $parameters['database'];
		$table		= $parameters['table'];

		$query = $this->generateSelect($database, $table, $fields);
		$query .= $this->generateWhere($table, $filters);
		$query .= $this->generateLimit($offset, $limit);

		return $query;
	}

	public function create($parameters, $data)
	{
		$database	= $parameters['database'];
		$table		= $parameters['table'];

		$query = $this->generateInsert($database, $table, $data);

		$this->_hDB->query($query);

		$id = $this->_hDB->getInsertedId();
		return $id;
	}

	public function update($parameters, $id, $data)
	{
		$database	= $parameters['database'];
		$table		= $parameters['table'];

		$filters	= array();
		$filters[]	= array($parameters['primary_key'], '=', $id);

		$query = $this->generateUpdate($database, $table, $data);
		$query .= $this->generateWhere($filters);

		$this->_hDB->query($query);

		$count = $this->_hDB->getAffectedRows();
		if($count !== 1)
		{
			throw new \Exception('Update operation operated on ' . $count . ' row(s)!');
		}
		return TRUE;
	}

	public function delete($parameters, $filters)
	{

	}
}

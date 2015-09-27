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
			$data[$id] = $o;
		}

		return $data;
	}

	public function generateQuery($parameters, $fields, $filters, $offset, $limit)
	{
		$database	= $parameters['database'];
		$table		= $parameters['table'];

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

		/**
		 * Restrictions.
		 */
		$where = array();
		foreach($filters AS $filter)
		{
			$where[] = '`' . $table . '`.`'. $filter[0] . '`' . $filter[1] . '"' . $filter[2] . '"';
		}

		if(!empty($where))
		{
			$query .= ' WHERE ' . join(' AND ', $where);
		}

		return $query;
	}
}

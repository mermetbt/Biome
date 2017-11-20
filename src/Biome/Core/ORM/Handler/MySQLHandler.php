<?php

namespace Biome\Core\ORM\Handler;

use Biome\Core\ORM\QuerySet;
use Biome\Core\ORM\LazyFetcher;
use Biome\Biome;

use Biome\Core\ORM\Exception\InvalidParameterException;

class MySQLHandler
{
	public function __construct(QuerySet $qs)
	{
	}

	protected function db()
	{
		return Biome::getService('mysql');
	}

	public function processSelect($select, $objectMapper, &$total_count)
	{
		$result = $this->db()->query($select);
		$found_rows = $this->db()->query('SELECT FOUND_ROWS() AS total;');

		while($row = $found_rows->fetch_assoc())
		{
			$total_count = $row['total'];
		}

		$data = array();
		while($row = $result->fetch_assoc())
		{
			$o = $objectMapper($row);
			$id = $o->getId();
			if($id === NULL)
			{
				throw new \Exception('No ID for object : ' . print_r($row, true));
			}

			if(is_array($id))
			{
				$id = join(',', $id);
			}
			$data[$id] = $o;
		}

		return $data;
	}

	protected function generateInsert($database, $table, $fields)
	{
		$query = 'INSERT INTO `' . $database . '`.`' . $table . '` SET ';

		$groups = array();
		foreach($fields AS $field_name => $value)
		{
			if($value === NULL)
			{
				$groups[] = '`' . $field_name . '`=NULL';
				continue;
			}
			try {
				$groups[] = '`' . $field_name . '`="' . $this->db()->real_escape_string($value) . '"';
			} catch(InvalidParameterException $e) {
				throw new InvalidParameterException('Field ' . $field_name . ' has invalid type! ' . $e->getMessage());
			}
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
				$groups[] = '`' . $field_name . '`=NULL';
				continue;
			}
			try {
				$groups[] = '`' . $field_name . '`="' . $this->db()->real_escape_string($value) . '"';
			} catch(InvalidParameterException $e) {
				throw new InvalidParameterException('Field ' . $field_name . ' has invalid type! ' . $e->getMessage());
			}
		}

		$query .= join(', ', $groups);

		return $query;
	}

	protected function generateDelete($database, $table)
	{
		$query = 'DELETE FROM `' . $database . '`.`' . $table . '` ';
		return $query;
	}

	protected function handleValue($value)
	{
		if($value instanceof LazyFetcher)
		{
			$values = $value->fetch();
			if(is_array($values))
			{
				foreach($values AS $i => $v)
				{
					$values[$i] = $this->db()->real_escape_string($v);
				}
			}
			return $values;
		}

		return $this->db()->real_escape_string($value);
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
		$wheres = array();
		foreach($filters AS $filter)
		{
			$column = $this->db()->real_escape_string($filter[0]);
			$operator = $this->db()->real_escape_string($filter[1]);

			$value = $this->handleValue($filter[2]);

			$where = '`' . $table . '`.`'. $column . '` ' . $operator . ' ';
			if(is_array($value))
			{
				if(empty($value))
				{
					$where .= '(NULL)';
				}
				else
				{
					$where .= '(' . join(', ', $value) . ')';
				}
			}
			else
			{
				$where .= '"' . $value . '"';
			}

			$wheres[] = $where;
		}

		return ' WHERE ' . join(' AND ', $wheres);
	}

	protected function generateOrders($orders)
	{
		return ' ORDER BY ' . join(', ', $orders);
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

	public function create($parameters, $data)
	{
		$database	= !empty($parameters['database']) ? $parameters['database'] : $this->db()->getDatabase();
		$table		= $parameters['table'];

		$query = $this->generateInsert($database, $table, $data);

		$this->db()->query($query);

		$id = $this->db()->getInsertedId();

		// Case of multiple primary key without autoincrement.
		if(is_array($parameters['primary_key']))
		{
			$id = array();
			foreach($parameters['primary_key'] AS $pk)
			{
				$id[] = $data[$pk];
			}
		}

		return $id;
	}

	public function update($parameters, $id, $data)
	{
		$database	= !empty($parameters['database']) ? $parameters['database'] : $this->db()->getDatabase();
		$table		= $parameters['table'];

		$filters	= array();

		if(is_array($parameters['primary_key']))
		{
			foreach($parameters['primary_key'] AS $index => $pk)
			{
				$filters[]	= array($pk, '=', $id[$index]);
			}
		}
		else
		{
			$filters[]	= array($parameters['primary_key'], '=', $id);
		}

		$query = $this->generateUpdate($database, $table, $data);
		$query .= $this->generateWhere($table, $filters);

		$this->db()->query($query);

		$count = $this->db()->getAffectedRows();
		if($count > 1)
		{
			throw new \Exception('Update operation operated on ' . $count . ' row(s)! (' . $query . ')');
		}
		return TRUE;
	}

	public function delete($parameters, $id)
	{
		$database	= !empty($parameters['database']) ? $parameters['database'] : $this->db()->getDatabase();
		$table		= $parameters['table'];

		$filters	= array();
		if(is_array($parameters['primary_key']))
		{
			foreach($parameters['primary_key'] AS $index => $pk)
			{
				$filters[]	= array($pk, '=', $id[$index]);
			}
		}
		else
		{
			$filters[]	= array($parameters['primary_key'], '=', $id);
		}

		$query = $this->generateDelete($database, $table);
		$query .= $this->generateWhere($table, $filters);

		$this->db()->query($query);

		$count = $this->db()->getAffectedRows();
		if($count > 1)
		{
			throw new \Exception('Delete operation operated on ' . $count . ' row(s)! (' . $query . ')');
		}
		return TRUE;
	}
}

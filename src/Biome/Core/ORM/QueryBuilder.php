<?php

namespace Biome\Core\ORM;

use Biome\Core\ORM\LazyFetcher;
use Biome\Core\ORM\Filter\FilterNode;
use Biome\Core\ORM\Filter\OperandHandlerInterface;
use Biome\Biome;

class QueryBuilder implements OperandHandlerInterface
{
	protected $database;
	protected $operation;
	protected $columns = array('*');
	protected $from;
	protected $innerjoins = array();
	protected $wheres = NULL;
	protected $orders = array();
	protected $offset = NULL;
	protected $limit = NULL;

	public function __construct($database, $table_name)
	{
		$this->database = $database;
		$this->from = $table_name;
	}

	public static function table($table_name)
	{
		return new DB(NULL, $table_name);
	}

	public function select($columns = ['*'])
	{
		$this->operation = 'SELECT SQL_CALC_FOUND_ROWS';
		$this->columns = is_array($columns) ? $columns : func_get_args();
		return $this;
	}

	public function from($table)
	{
		$this->from = $table;
		return $this;
	}

	public function join($table, $one, $operator, $two)
	{
		$join_key = $table . $one . $operator . print_r($two, true);
		$this->innerjoins[$join_key] = array($table, $one, $operator, $two);
		return $this;
	}

	public function where($column, $operator = null, $value = null)
	{
		if($column instanceof FilterNode)
		{
			$this->wheres = new FilterNode('AND', array($this->wheres, $column));
		}
		else
		{
			$this->wheres = new FilterNode('AND', array($this->wheres, array($operator, $column, $value)));
		}
		return $this;
	}

	public function orWhere($column, $operator = null, $value = null)
	{
		if($column instanceof FilterNode)
		{
			$this->wheres = new FilterNode('OR', array($this->wheres, $column));
		}
		else
		{
			$this->wheres = new FilterNode('OR', array($this->wheres, array($operator, $column, $value)));
		}
		return $this;
	}

	public function setFilters(FilterNode $filters)
	{
		$this->wheres = $filters;
		return $this;
	}

	public function orderby($orderby)
	{
		$this->orders = is_array($orderby) ? $orderby : func_get_args();
		return $this;
	}

	public function offset($offset)
	{
		$this->offset = $offset;
		return $this;
	}

	public function limit($limit)
	{
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Query generation.
	 */
	protected function db()
	{
		return Biome::getService('mysql');
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

	public function handleOperand($operand)
	{
		$operator = $operand[0];
		$field = $operand[1];
		$value = $operand[2];

		if(is_array($field))
		{
			$table 	= $this->db()->real_escape_string($field[0]);
			$column	= $this->db()->real_escape_string($field[1]);
			$column = '`' . $table . '`.`'. $column . '`';
		}
		else
		{
			$column = $this->db()->real_escape_string($field);
			$column = '`' . $this->from . '`.`'. $column . '`';
		}
		$operator = $this->db()->real_escape_string($operator);

		$value = $this->handleValue($value);

		$where_sql = $column . ' ' . $operator . ' ';
		if(is_array($value))
		{
			if(empty($value))
			{
				$where_sql .= '(NULL)';
			}
			else
			{
				$where_sql .= '(' . join(', ', $value) . ')';
			}
		}
		else
		{
			$where_sql .= '"' . $value . '"';
		}
		return $where_sql;
	}

	public function toSql()
	{
		if(empty($this->operation))
		{
			$this->select();
		}

		$columns = array();
		foreach($this->columns AS $column)
		{
			if(is_array($column))
			{
				$columns[] = $column[0] . '.' . $column[1];
			}
			else
			{
				$columns[] = $this->from . '.' . $column;
			}
		}

		$sql = $this->operation . ' ' . join(',', $columns);

		$sql .= ' FROM ' . (!empty($this->database) ? $this->database . '.' : '') . $this->from;

		if(!empty($this->innerjoins))
		{
			foreach($this->innerjoins AS $join)
			{
				$table		= $join[0];
				$one		= $join[1];
				$operator	= $join[2];
				$two		= $join[3];

				if(is_array($two))
				{
					$two = $two[0] . '.' . $two[1];
				}
				else
				{
					$two = $this->from . '.' . $two;
				}

				$sql .= ' LEFT JOIN ' . $table . ' ON ' . $table . '.' . $one . $operator . $two;
			}
		}

		if(!empty($this->wheres))
		{
			$where_sql = $this->wheres->toSql($this);
			if(!empty($where_sql))
			{
				$sql .= ' WHERE ' . $where_sql;
			}
		}

		if(!empty($this->orders))
		{
			$sql .= ' ORDER BY ' . join(',', $this->orders);
		}

		if($this->offset !== NULL && $this->limit !== NULL)
		{
			$sql .= ' LIMIT ' . $this->offset . ',' . $this->limit;
		}
		else
		if($this->offset !== NULL)
		{
			$sql .= ' LIMIT ' . $this->limit;
		}

		return $sql;
	}
}

<?php

namespace Biome\Core\ORM\Connector;

use Biome\Core\Logger\Logger;
use Biome\Core\ORM\Exception\DuplicateException;

class MySQLConnector
{
	protected static $_instances = array();

	protected $_instance	= NULL;
	protected $_parameters	= array();

	protected $_logger		= FALSE;
	protected $_queries_log	= array();

	protected $_last_query = '';

	private function __construct() { }

	public static function getInstance($name = 'default')
	{
		if(!isset(self::$_instances[$name]))
		{
			self::$_instances[$name] = new MySQLConnector();
		}
		return self::$_instances[$name];
	}

	public function setConnectionParameters(array $parameters)
	{
		$this->_parameters = $parameters;
	}

	public function connect()
	{
		$this->_instance = new \MySQLi(
					$this->_parameters['hostname'],
					$this->_parameters['username'],
					$this->_parameters['password']);

		if(!$this->_instance)
		{
			throw new \Exception('Unable to connect to the database!');
		}

		if(isset($this->_parameters['database']))
		{
			$this->_instance->select_db($this->_parameters['database']);
		}

		$this->_instance->autocommit(FALSE);
		$this->_instance->set_charset('utf8');

		return TRUE;
	}

	public function setQueryLogger($enable = TRUE)
	{
		$this->_logger = $enable === TRUE;
		return $this->_logger;
	}

	public function getQueriesLog()
	{
		return $this->_queries_log;
	}

	public function getDatabase()
	{
		if(isset($this->_parameters['database']))
		{
			return $this->_parameters['database'];
		}
		return NULL;
	}

	public function isConnected()
	{
		if($this->_instance == NULL)
		{
			return FALSE;
		}
		return TRUE;
	}

	public function close()
	{
		if(!$this->isConnected())
		{
			return FALSE;
		}
		$this->_instance->close();
		return TRUE;
	}

	public function real_escape_string($string)
	{
		if(!$this->isConnected())
		{
			$this->connect();
		}

		if($string === NULL)
		{
			return '';
		}

		if($string === FALSE)
		{
			return 0;
		}

		if($string === TRUE)
		{
			return 1;
		}

		if(!is_string($string) && !is_numeric($string))
		{
			$data = var_export($string, true);
			Logger::error('Real escape string expects a string! Content: ' . $data);
			throw new \Exception('Real escape string expects a string!' . PHP_EOL . 'Content : ' . PHP_EOL . $data);
		}
		return $this->_instance->real_escape_string($string);
	}

	public function query($query)
	{
		if(!$this->isConnected())
		{
			$this->connect();
		}

		if($this->_logger)
		{
			$this->_queries_log[] = $query;
		}

		Logger::debug('Executing query: ' . $query);
		$this->_last_query = $query;
		$result = $this->_instance->query($query);

		$this->checkDbError();

		return $result;
	}

	public function getInsertedId()
	{
		if(!$this->isConnected())
		{
			return FALSE;
		}

		if($id = $this->_instance->insert_id)
		{
			return $id;
		}
		return FALSE;
	}

	public function getAffectedRows()
	{
		if(!$this->isConnected())
		{
			return FALSE;
		}
		return $this->_instance->affected_rows;
	}

	public function commit()
	{
		if(!$this->isConnected())
		{
			return FALSE;
		}

		$this->checkDbError();

		$this->_instance->commit();

		return TRUE;
	}

	public function rollback()
	{
		if(!$this->isConnected())
		{
			return FALSE;
		}

		$this->_instance->rollback();
		return TRUE;
	}

	protected function checkDbError()
	{
		if(!empty($this->_instance->error))
		{
			Logger::error('SQL Error: ' . $this->_instance->error);

			switch($this->_instance->errno)
			{
				case 1022:
					throw new DuplicateException($this->_instance->errno);
				default:
					throw new \Exception('SQL Error: ' . $this->_instance->error . ' Last Query:(' . $this->_last_query . ')');
			}
		}

		if($this->_instance->warning_count != 0)
		{
			$message = '';
			if ($result = $this->_instance->query("SHOW WARNINGS"))
			{
				while($row = $result->fetch_row())
				{
					$message .= $row[0] . ' (' . $row[1] . '): ' . $row[2] . PHP_EOL;
				}
				$result->close();
			}
			Logger::error('SQL Warning: ' . $message);
			throw new \Exception('SQL Warning: ' . $message . ' Last Query:(' . $this->_last_query . ')');
		}
		return TRUE;
	}

	public function getTimezoneOffset()
	{
		$now = new \DateTime();
		$mins = $now->getOffset() / 60;

		$sgn = ($mins < 0 ? -1 : 1);
		$mins = abs($mins);
		$hrs = floor($mins / 60);
		$mins -= $hrs * 60;

		$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
		return $offset;
	}

	public function setTimezone($timezone)
	{
		$offset = $this->getTimezoneOffset();
		$this->query('SET time_zone=\''.$offset.'\';', NULL);
	}
}

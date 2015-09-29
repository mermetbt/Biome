<?php

namespace Biome\Core\ORM\Connector;

class MySQLConnector
{
	protected static $_instances = array();

	protected $_instance	= NULL;
	protected $_parameters	= array();

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

		$this->_instance->autocommit(FALSE);
		$this->_instance->set_charset('utf8');
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
		if($string === NULL)
		{
			return '';
		}
		if(!is_string($string) && !is_numeric($string))
		{
			$data = print_r($string, true);
			throw new \Exception('Real escape string expects a string !' . PHP_EOL . 'Content : ' . PHP_EOL . $data);
		}
		return $this->_instance->real_escape_string($string);
	}

	public function query($query)
	{
		if(!$this->isConnected())
		{
			$this->connect();
		}

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
			throw new \Exception('SQL Error: ' . $this->_instance->error);
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
			throw new \Exception('SQL Warning: ' . $message);
		}
		return TRUE;
	}
}
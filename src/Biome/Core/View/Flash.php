<?php

namespace Biome\Core\View;

class Flash
{
	protected static $_instance = NULL;

	protected $errors = array();
	protected $warnings = array();
	protected $infos = array();
	protected $success = array();

	private function __construct() {}

	public function __destruct()
	{
		if($this->hasMessages())
		{
			$_SESSION['flash'] = serialize($this);
		}
	}

	public static function getInstance()
	{
		/* Deserialization if exists. */
		if(isset($_SESSION['flash']))
		{
			self::$_instance = unserialize($_SESSION['flash']);
			unset($_SESSION['flash']);
		}

		if(!self::$_instance)
		{
			self::$_instance = new Flash();
		}
		return self::$_instance;
	}

	public function hasMessages()
	{
		return	!empty($this->errors) ||
				!empty($this->warnings) ||
				!empty($this->infos) ||
				!empty($this->success);
	}

	public function flushMessages()
	{
		unset($this->errors);
		unset($this->warnings);
		unset($this->infos);
		unset($this->success);
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getWarnings()
	{
		return $this->warnings;
	}

	public function getInfos()
	{
		return $this->infos;
	}

	public function getSuccess()
	{
		return $this->success;
	}

	public function error($title, $message = '')
	{
		$this->errors[$title] = $message;
		return $this;
	}

	public function warning($title, $message = '')
	{
		$this->warnings[$title] = $message;
		return $this;
	}

	public function info($title, $message = '')
	{
		$this->infos[$title] = $message;
		return $this;
	}

	public function success($title, $message = '')
	{
		$this->success[$title] = $message;
		return $this;
	}
}

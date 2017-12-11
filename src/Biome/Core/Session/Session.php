<?php

namespace Biome\Core\Session;

class Session {
	public function __construct()
	{
		$this->setName(md5($_SERVER['PHP_SELF']));
	}

	public function setName($name)
	{
		session_name($name);
	}

	public function start()
	{
		session_start();
	}

	public function destroy()
	{
		session_destroy();
	}

	public function setId($session_id)
	{
		session_id($session_id);
	}

	public function getId()
	{
		return session_id();
	}

	public function set($name, $value)
	{
		$_SESSION[$name] = $value;
	}

	public function get($name)
	{
		if(!$this->exists($name))
		{
			return NULL;
		}

		return $_SESSION[$name];
	}

	public function exists($name)
	{
		return array_key_exists($name, $_SESSION);
	}

	public function remove($name)
	{
		unset($_SESSION[$name]);
	}
}


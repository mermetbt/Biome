<?php

namespace Biome\Core\Collection;

use Biome\Core\Collection;

class SessionCollection extends Collection
{
	private $_session = NULL;

	public function __construct()
	{
		$this->_session = \Biome\Biome::getService('session');

		// Load from session.
		$class_name = get_class($this);

		if(!empty($_SESSION['collections'][$class_name]))
		{
			$data = $_SESSION['collections'][$class_name];
			$this->unserialize($data);
		}
	}

	public function __destruct()
	{
		// Store to session.
		$class_name = get_class($this);
		$data = $this->serialize();

		if(!isset($_SESSION['collections']))
		{
			$_SESSION['collections'] = array();
		}
		$_SESSION['collections'][$class_name] = $data;
	}
}

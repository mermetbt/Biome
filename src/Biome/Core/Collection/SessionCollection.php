<?php

namespace Biome\Core\Collection;

use Biome\Core\Collection;

class SessionCollection extends Collection
{
	public function __construct()
	{
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

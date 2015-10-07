<?php

namespace Biome\Core\Collection;

use Biome\Core\Collection;

class RequestCollection extends Collection
{
	private $_view_state = NULL;

	public function __construct()
	{
		// Load from session.
		$class_name = get_class($this);

		if(!empty($_SESSION['collections_req'][$class_name]))
		{
			$data = $_SESSION['collections_req'][$class_name];
			$this->unserialize($data);
			$this->_view_state = $_SESSION['collections_req_vs'][$class_name];
		}
		else
		{
			$view_state = \Biome\Biome::getService('view')->getViewState();
			$this->_view_state = $view_state;
		}
	}

	public function __destruct()
	{
		$class_name = get_class($this);
		$view_state = \Biome\Biome::getService('view')->getViewState();

		if($view_state != $this->_view_state)
		{
			unset($_SESSION['collections_req'][$class_name]);
			unset($_SESSION['collections_req_vs'][$class_name]);
			return;
		}

		// Store to session.
		$data = $this->serialize();

		if(!isset($_SESSION['collections_req']))
		{
			$_SESSION['collections_req'] = array();
		}
		$_SESSION['collections_req'][$class_name] = $data;
		$_SESSION['collections_req_vs'][$class_name] = $view_state;
	}
}

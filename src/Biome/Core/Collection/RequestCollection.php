<?php

namespace Biome\Core\Collection;

use Biome\Core\Collection;
use Biome\Biome;

class RequestCollection extends Collection
{
	private $_session = NULL;

	public function __construct()
	{
		$this->_session = \Biome\Biome::getService('session');

		// Load from session.
		$class_name = get_class($this);

		$view_state = Biome::getService('request')->request->get('_token');

		if(!empty($_SESSION['collections_req'][$class_name][$view_state]))
		{
			$data = $_SESSION['collections_req'][$class_name][$view_state];
			$this->unserialize($data);
		}
		else
		{
			unset($_SESSION['collections_req'][$class_name]);
		}
	}

	public function __destruct()
	{
		$class_name = get_class($this);
		$view_state = Biome::getService('view')->getViewState();

		// Store to session.
		$data = $this->serialize();

		if(!isset($_SESSION['collections_req']))
		{
			$_SESSION['collections_req'] = array();
		}
		$_SESSION['collections_req'][$class_name][$view_state] = $data;
	}
}

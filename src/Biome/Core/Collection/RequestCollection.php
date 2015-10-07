<?php

namespace Biome\Core\Collection;

use Biome\Core\Collection;
use Biome\Biome;

class RequestCollection extends Collection
{
	public function __construct()
	{
		// Load from session.
		$class_name = get_class($this);

		$view_state = Biome::getService('request')->request->get('_token');

		if(!empty($_SESSION['collections_req'][$view_state][$class_name]))
		{
			$data = $_SESSION['collections_req'][$view_state][$class_name];
			$this->unserialize($data);
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
		$_SESSION['collections_req'][$view_state][$class_name] = $data;
	}
}

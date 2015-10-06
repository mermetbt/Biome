<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class AComponent extends Component
{
	public function getURL()
	{
		$controller = NULL;
		$action = NULL;

		if(isset($this->attributes['controller']))
		{
			$controller = $this->attributes['controller'];
		}

		if(isset($this->attributes['action']))
		{
			$action = $this->attributes['action'];
		}

		return \URL::fromRoute($controller, $action);
	}
}

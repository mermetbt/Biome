<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class AComponent extends Component
{
	public function getURL()
	{
		$controller = NULL;
		$action = NULL;
		$item = NULL;
		$module = NULL;

		if(isset($this->attributes['controller']))
		{
			$controller = $this->attributes['controller'];
		}

		if(isset($this->attributes['action']))
		{
			$action = $this->attributes['action'];
		}

		if(isset($this->attributes['item']))
		{
			$item = $this->fetchValue($this->attributes['item']);
		}

		if(isset($this->attributes['module']))
		{
			$module = $this->attributes['module'];
		}

		return \URL::fromRoute($controller, $action, $item, $module);
	}
}

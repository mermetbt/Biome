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
		$page = NULL;

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

		if(isset($this->attributes['page']))
		{
			$page = $this->fetchValue($this->attributes['page']);
		}

		return \URL::fromRoute($controller, $action, $item, $module, $page);
	}

	public function isAllowed()
	{
		$rights = \Biome\Biome::getService('rights');

		if(isset($this->attributes['controller']))
		{
			$controller = $this->attributes['controller'];
		}
		else
		{
			$controller = 'index';
		}

		if(isset($this->attributes['action']))
		{
			$action = $this->attributes['action'];
		}
		else
		{
			$action = 'index';
		}

		return $rights->isRouteAllowed('GET', $controller, $action);
	}
}

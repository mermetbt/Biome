<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class AComponent extends Component
{
	public function getURL()
	{
		$controller	= $this->getAttribute('controller', NULL);
		$action		= $this->getAttribute('action', NULL);
		$item		= $this->getAttribute('item', NULL);
		$module		= $this->getAttribute('module', NULL);
		$page		= $this->getAttribute('page', NULL);

		if($item)
		{
			$item = $this->fetchValue($item);
		}

		if($page)
		{
			$page = $this->fetchValue($page);
		}

		return \URL::fromRoute($controller, $action, $item, $module, $page);
	}

	public function isAllowed()
	{
		$rights = \Biome\Biome::getService('rights');

		$controller	= $this->getAttribute('controller', 'index');
		$action		= $this->getAttribute('action', 'index');

		return $rights->isRouteAllowed('GET', $controller, $action);
	}
}

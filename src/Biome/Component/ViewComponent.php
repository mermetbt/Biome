<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class ViewComponent extends Component
{
	public function getAction()
	{
		if(!isset($this->attributes['action']))
		{
			return NULL;
		}
		return $this->attributes['action'];
	}
}

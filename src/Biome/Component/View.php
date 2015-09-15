<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class View extends Component
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

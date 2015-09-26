<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class Form extends Component
{
	public function getAction()
	{
		if(!empty($this->attributes['controller']) && !empty($this->attributes['action']))
		{
			return \URL::fromRoute($this->attributes['controller'], $this->attributes['action']);
		}
		return NULL;
	}
}

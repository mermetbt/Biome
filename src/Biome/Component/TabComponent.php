<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class TabComponent extends Component
{
	public function getTitle()
	{
		if(!isset($this->attributes['title']))
		{
			return '';
		}
		return $this->attributes['title'];
	}
}

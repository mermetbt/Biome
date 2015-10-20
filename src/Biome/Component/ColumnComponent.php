<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class ColumnComponent extends Component
{
	public function getTitle()
	{
		if(!isset($this->attributes['headerTitle']))
		{
			return '';
		}
		return $this->attributes['headerTitle'];
	}
}

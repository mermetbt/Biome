<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class Button extends Component
{
	public function getValue()
	{
		if(isset($this->attributes['value']))
		{
			return $this->attributes['value'];
		}
		return 'Submit';
	}
}

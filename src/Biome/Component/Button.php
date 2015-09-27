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

	public function getClass()
	{
		if(isset($this->attributes['class']))
		{
			return 'btn btn-default ' . $this->attributes['class'];
		}
		return 'btn btn-default';
	}
}

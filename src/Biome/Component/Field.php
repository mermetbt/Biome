<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class Field extends Component
{
	public function getName()
	{
		if(!isset($this->attributes['name']))
		{
			return str_replace('.', '/', $this->fetchVariable($this->attributes['value']));
		}
		$this->name = $this->attributes['name'];
	}

	public function getValue()
	{
		return $this->fetchValue($this->attributes['value']);
	}
}

<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class VariableComponent extends Component
{
	public function getValue()
	{
		return $this->fetchValue($this->attributes['value']);
	}
}
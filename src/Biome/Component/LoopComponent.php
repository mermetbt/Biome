<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class LoopComponent extends Component
{
	public function getVar()
	{
		return $this->attributes['var'];
	}

	public function getValue()
	{
		return $this->fetchValue($this->attributes['value']);
	}
}

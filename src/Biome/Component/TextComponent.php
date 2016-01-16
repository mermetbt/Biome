<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class TextComponent extends Component
{
	public function getValue()
	{
		$value = $this->getAttribute('value', NULL);
		return $value;
	}
}

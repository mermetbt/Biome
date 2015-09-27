<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class Loop extends Component
{
	public function render()
	{
		$var = $this->attributes['var'];

		$inner = '';
		foreach($this->getValue() AS $v)
		{
			$this->setContext($var, $v);
			$inner .= $this->renderChildren();
			$this->unsetContext($var);
		}

		return $this->renderComponent($inner);
	}

	public function getValue()
	{
		return $this->fetchValue($this->attributes['value']);
	}
}

<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class Views extends Component
{
	public function load($action)
	{
		foreach($this->value AS $index => $v)
		{
			if($v instanceof View)
			{
				if($v->getAction() != $action)
				{
					unset($this->value[$index]);
				}
			}
		}
	}
}

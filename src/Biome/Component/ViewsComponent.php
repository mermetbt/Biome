<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class ViewsComponent extends Component
{
	public function load($action)
	{
		foreach($this->value AS $index => $v)
		{
			if($v instanceof ViewComponent)
			{
				if($v->getAction() != $action)
				{
					unset($this->value[$index]);
				}
			}
		}
	}
}

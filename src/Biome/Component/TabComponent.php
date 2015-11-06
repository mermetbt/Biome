<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class TabComponent extends Component
{
	public function getTitle()
	{
		return $this->getAttribute('title', '');
	}
}

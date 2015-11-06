<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class TitleComponent extends Component
{
	public function getTitle()
	{
		return $this->getAttribute('title', '');
	}
}

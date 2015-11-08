<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class TitleComponent extends Component
{
	public function getTitle()
	{
		$title = $this->getAttribute('title', '');
		return $this->fetchValue($title);
	}
}

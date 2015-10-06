<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\View\Flash;

class FlashComponent extends Component
{
	public function getFlash()
	{
		return Flash::getInstance();
	}
}

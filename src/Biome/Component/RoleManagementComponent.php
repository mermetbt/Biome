<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class RoleManagementComponent extends Component
{
	public function getTitle()
	{
		return $this->getAttribute('title', '');
	}

	public function getValue()
	{
		return $this->fetchValue($this->getAttribute('value', NULL));
	}
}

<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class RoleManagementComponent extends Component
{
	public function getTitle()
	{
		if(!isset($this->attributes['title']))
		{
			return '';
		}
		return $this->attributes['title'];
	}

	public function getValue()
	{
		if(!isset($this->attributes['value']))
		{
			return NULL;
		}

		return $this->fetchValue($this->attributes['value']);
	}
}

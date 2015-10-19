<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class SidebarComponent extends Component
{
	public function getValue()
	{
		$value = $this->fetchValue($this->attributes['value']);
		if(!$value instanceof QuerySet && !is_array($value))
		{
			throw new \Exception(	'Unable to loop on a value which is not a QuerySet or an array! '.
									'Value: ' . $this->attributes['value'] . ' '.
									'Result: ' . var_export($value)
			);
		}
		return $value;
	}
}

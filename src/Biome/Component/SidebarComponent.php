<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class SidebarComponent extends Component
{
	public function getValue()
	{
		$value_var = $this->getAttribute('value', NULL);
		$value = $this->fetchValue($value_var);
		if(!$value instanceof QuerySet && !is_array($value))
		{
			throw new \Exception(	'Unable to loop on a value which is not a QuerySet or an array! '.
									'Value: ' . $value_var . ' '.
									'Result: ' . var_export($value)
			);
		}
		return $value;
	}
}

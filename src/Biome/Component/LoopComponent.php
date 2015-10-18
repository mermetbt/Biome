<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\ORM\QuerySet;

class LoopComponent extends Component
{
	public function getVar()
	{
		return $this->attributes['var'];
	}

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

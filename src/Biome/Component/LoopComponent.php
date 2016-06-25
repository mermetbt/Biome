<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\ORM\QuerySet;

class LoopComponent extends Component
{
	public function getVar()
	{
		return $this->getAttribute('var');
	}

	public function getKeyName()
	{
		return $this->getAttribute('keyname', 'keyname');
	}

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

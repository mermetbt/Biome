<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;

class EnumField extends AbstractField
{
	protected $enumeration = array();

	public function __construct(array $enumeration)
	{
		$this->enumeration = $enumeration;
	}

	public function getEnumeration()
	{
		return $this->enumeration;
	}
}

<?php

namespace Biome\Core\ORM;

class RawSQL
{
	protected $_attribute = NULL;

	protected function __construct($attribute)
	{
		$this->_attribute = $attribute;
	}

	public static function select($attribute)
	{
		return new RawSQL($attribute);
	}

	public function get()
	{
		return $this->_attribute;
	}
}

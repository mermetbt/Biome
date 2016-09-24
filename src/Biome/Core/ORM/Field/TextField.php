<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;
use Biome\Core\ORM\Exception\InvalidParameterException;

class TextField extends AbstractField
{
	protected $size;

	public function __construct($size = 64)
	{
		if(!is_integer($size))
		{
			throw new InvalidParameterException('The "size" parameter of TextField have to be a number, current value: ' . $size);
		}
		$this->size = $size;
	}

	public function setSize($size)
	{
		$this->size = $size;
		return $this;
	}

	public function getSize()
	{
		return $this->size;
	}

	public function validate($object, $field_name)
	{
		if(!parent::validate($object, $field_name))
		{
			return FALSE;
		}

		$value = $object->$field_name;
		if(empty($value))
		{
			return TRUE;
		}

		if(strlen($value) > $this->size)
		{
			$this->setError('wrong_value', 'Field "' . $this->getLabel() . '" (' . $field_name . ') content is too long ' . strlen($value) . ' characters, only ' . $this->size . ' allowed!');
			return FALSE;
		}
		return TRUE;
	}
}

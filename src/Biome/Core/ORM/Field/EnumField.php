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

		if(empty($this->enumeration[$value]))
		{
			$this->setError('wrong_value', 'Field "' . $this->getLabel() . '" (' . $field_name . ') should contains one of this options: ' . join('/', array_keys($this->enumeration)));
			return FALSE;
		}

		return TRUE;
	}
}

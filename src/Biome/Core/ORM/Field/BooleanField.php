<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;

class BooleanField extends AbstractField
{
	public function __construct()
	{
	}

	public function validate($object, $field_name)
	{
		if(!parent::validate($object, $field_name))
		{
			return FALSE;
		}

		$value = $object->$field_name;
		if($value === NULL || $valu === '')
		{
			return TRUE;
		}

		if($value !== FALSE && $value !== TRUE && $value !== 0 && $value !== 1)
		{
			$this->setError('wrong_value', 'Field "' . $this->getLabel() . '" (' . $field_name . ') is not a valid boolean (true, false, 0, 1)!');
			return FALSE;
		}

		return TRUE;
	}
}

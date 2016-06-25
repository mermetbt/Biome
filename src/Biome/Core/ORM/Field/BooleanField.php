<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;

class BooleanField extends AbstractField
{
	public function __construct()
	{
	}

	public function applySet($value)
	{
		$value = parent::applySet($value);

		if(is_string($value))
		{
			$value = strtolower($value);
			if($value == 'true')
			{
				$value = TRUE;
			}
			else
			if($value == 'false')
			{
				$value = FALSE;
			}
		}

		$value = ($value == TRUE) ? 1 : 0;
		return $value;
	}

	public function validate($object, $field_name)
	{
		if(!$object->hasFieldValueChanged($field_name))
		{
			return TRUE;
		}

		$value = $object->$field_name;
		if($this->isRequired() && ($value === NULL || $value === ''))
		{
			$this->setError('required', 'Field "' . $this->getLabel() . '" is required!');
			return FALSE;
		}

		if($value === NULL || $value === '')
		{
			return TRUE;
		}

		if($value !== FALSE && $value !== TRUE && $value !== 0 && $value !== 1 && $value !== '0' && $value !== '1')
		{
			$this->setError('wrong_value', 'Field "' . $this->getLabel() . '" (' . $field_name . ') is not a valid boolean (true, false, 0, 1)!');
			return FALSE;
		}

		return TRUE;
	}
}

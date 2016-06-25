<?php

namespace Biome\Core\ORM\Field;

class EmailField extends TextField
{
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

		if(!filter_var($value, FILTER_VALIDATE_EMAIL))
		{
			$this->setError('wrong_value', 'Field "' . $this->getLabel() . '" (' . $field_name . ') is not a valid mail address!');
			return FALSE;
		}

		return TRUE;
	}
}

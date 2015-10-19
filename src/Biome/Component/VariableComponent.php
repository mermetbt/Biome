<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\ORM\AbstractField;

class VariableComponent extends Component
{
	public function getValue()
	{
		if(!isset($this->attributes['value']))
		{
			return NULL;
		}

		return $this->fetchValue($this->attributes['value']);
	}

	public function getField()
	{
		if(empty($this->attributes['value']))
		{
			return NULL;
		}

		$field = $this->fetchField($this->attributes['value']);

		return $field;
	}

	public function getLabel()
	{
		if(!isset($this->attributes['label']))
		{
			$field = $this->getField();
			if(!$field instanceof AbstractField)
			{
				throw new \Exception('Attribute "label" must be defined on component!');
			}

			return $field->getLabel();
		}

		$this->name = $this->attributes['label'];

		return $this->name;
	}
}

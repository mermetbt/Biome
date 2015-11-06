<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\ORM\AbstractField;

class VariableComponent extends Component
{
	public function getValue()
	{
		$value = $this->getAttribute('value', NULL);
		return $this->fetchValue($value);
	}

	public function getField()
	{
		$value = $this->getAttribute('value', NULL);

		$field = $this->fetchField($value);

		return $field;
	}

	public function getLabel()
	{
		return $this->name = $this->getAttribute('label', function() {
			$field = $this->getField();
			if(!$field instanceof AbstractField)
			{
				throw new \Exception('Attribute "label" must be defined on component!');
			}

			return $field->getLabel();
		});
	}
}

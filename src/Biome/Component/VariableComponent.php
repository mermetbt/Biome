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

	public function getParentValue()
	{
		$value = $this->getAttribute('value', NULL);
		return $this->fetchParentValue($value);
	}

	public function getField()
	{
		$value = $this->getAttribute('value', NULL);

		$field = $this->fetchField($value);

		return $field;
	}

	public function getType()
	{
		return $this->getAttribute('type', function()
		{
			$field = $this->getField();

			if(!$field instanceof AbstractField)
			{
				throw new \Exception('Attribute "type" must be defined on field component!');
			}

			return $field->getType();
		});
	}

	public function getName()
	{
		$name = $this->getAttribute('name', function() {
			$value = $this->getAttribute('value');
			$variables = $this->fetchVariables($value);
			$name = '';
			foreach($variables AS $var)
			{
				$name .= str_replace('.', '/', $var);
			}
			if(empty($name))
			{
				$name = $value;
			}
			return $name;
		});

		return $this->name = $name;
	}

	public function getLabel()
	{
		return $this->name = $this->getAttribute('label', function() {
			$field = $this->getField();

			if($field == NULL)
			{
				return '';
			}

			if(!$field instanceof AbstractField)
			{
				throw new \Exception('Attribute "label" must be defined on component!');
			}

			return $field->getLabel();
		});
	}
}

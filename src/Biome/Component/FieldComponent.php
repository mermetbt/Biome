<?php

namespace Biome\Component;

use Biome\Core\View\Component;

use Biome\Core\ORM\AbstractField;

class FieldComponent extends VariableComponent
{
	public function render()
	{
		$this->addClasses('form-control');
		return parent::render();
	}

	public function getName()
	{
		$name = $this->getAttribute('name', function() {
			$variables = $this->fetchVariables($this->getAttribute('value'));
			$name = '';
			foreach($variables AS $var)
			{
				$name .= str_replace('.', '/', $var);
			}
			return $name;
		});

		return $this->name = $name;
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

	public function getErrors()
	{
		$field = $this->getField();

		if(empty($field))
		{
			return array();
		}

		return $field->getErrors();
	}

	public function getPlaceholder()
	{
		return $this->getAttribute('placeholder', '');
	}

	public function showErrors()
	{
		return $this->getAttribute('error', '1') == '1';
	}
}

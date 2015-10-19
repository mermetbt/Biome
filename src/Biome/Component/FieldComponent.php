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
		if(!isset($this->attributes['name']))
		{
			$variables = $this->fetchVariables($this->attributes['value']);
			$name = '';
			foreach($variables AS $var)
			{
				$name .= str_replace('.', '/', $var);
			}
			return $name;
		}
		$this->name = $this->attributes['name'];
	}

	public function getType()
	{
		if(isset($this->attributes['type']))
		{
			return $this->attributes['type'];
		}

		$field = $this->getField();

		if(!$field instanceof AbstractField)
		{
			throw new \Exception('Attribute "type" must be defined on field component!');
		}

		return $field->getType();
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
		if(isset($this->attributes['placeholder']))
		{
			return $this->attributes['placeholder'];
		}
		return '';
	}

	public function showErrors()
	{
		if(isset($this->attributes['error']))
		{
			return $this->attributes['error'] == '1';
		}
		return TRUE;
	}
}

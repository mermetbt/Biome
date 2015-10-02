<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class Field extends Component
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

	public function getValue()
	{
		return $this->fetchValue($this->attributes['value']);
	}

	public function getType()
	{
		if(isset($this->attributes['type']))
		{
			return $this->attributes['type'];
		}

		$field = $this->fetchField($this->attributes['value']);

		return $field->getType();
	}

	public function getPlaceholder()
	{
		if(isset($this->attributes['placeholder']))
		{
			return $this->attributes['placeholder'];
		}
		return '';
	}

	public function getLabel()
	{
		if(!isset($this->attributes['label']))
		{
			return $this->fetchField($this->attributes['value'])->getLabel();
		}
		$this->name = $this->attributes['label'];
	}
}

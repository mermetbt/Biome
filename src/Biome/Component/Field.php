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
			return str_replace('.', '/', $this->fetchVariable($this->attributes['value']));
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

		$type = $this->fetchType($this->attributes['value']);

		return $type;
	}

	public function getPlaceholder()
	{
		if(isset($this->attributes['placeholder']))
		{
			return $this->attributes['placeholder'];
		}
		return '';
	}
}

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

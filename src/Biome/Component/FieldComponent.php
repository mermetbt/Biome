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

	public function getValue()
	{
		$value = parent::getValue();
		if(empty($value))
		{
			$default = $this->getAttribute('default', '');
			$value = $this->fetchValue($default);
		}
		return $value;
	}

	public function getEditable()
	{
		$editable = $this->getAttribute('editable', TRUE);
		if(is_string($editable))
		{
			$editable = $this->fetchValue($editable);
		}

		if(strtolower($editable) === 'true' || $editable === '1' || $editable === TRUE)
		{
			return TRUE;
		}
		return FALSE;
	}
}

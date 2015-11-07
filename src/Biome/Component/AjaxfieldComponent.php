<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\ORM\AbstractField;
use Biome\Core\HTTP\Request;

class AjaxfieldComponent extends VariableComponent
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

	public function getPlaceholder()
	{
		return $this->getAttribute('placeholder', '');
	}

	public function handleAjaxRequest(Request $request)
	{
		$name = $this->getName();
		$value_name = $this->getAttribute('value');
		$variables = $this->fetchVariables($value_name);

		$variable = reset($variables);

		$raw = explode('.', $variable);
		$last = array_pop($raw);

		$variable = join('.', $raw);
		$object = $this->fetchValue('#{' . $variable . '}');

		if(!$object instanceof \Biome\Core\ORM\Models)
		{
			throw new \Exception('Unable to fetch the object from the variable: ' . $variable);
		}

		$object->$last = $request->get($name);

		$results = array();
		if($object->save())
		{
			$results['success'] = 'Success!';
			$results['value'] = $object->$last;
		}
		else
		{
			$results['errors'] = $object->getErrors();
		}
		echo json_encode($results);
		return TRUE;
	}
}

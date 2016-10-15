<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\HTTP\Request;

class ImageFieldComponent extends VariableComponent
{
	public function render()
	{
		$this->addClasses('form-control');
		return parent::render();
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

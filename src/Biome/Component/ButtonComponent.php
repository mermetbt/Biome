<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class ButtonComponent extends Component
{
	public function render()
	{
		$this->addClasses('btn btn-default');
		return parent::render();
	}

	public function getValue()
	{
		if(isset($this->attributes['value']))
		{
			return $this->attributes['value'];
		}
		return 'Submit';
	}

	public function getAction()
	{
		$action = $this->attributes['action'];

		$actions = $this->fetchVariables($action);

		$var = reset($actions);
		$raw = explode('.', $var);

		$controller = $raw[0];
		$action = $raw[1];

		return \URL::fromRoute($controller, $action);
	}
}

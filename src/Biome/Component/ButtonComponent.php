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

	public function getValue($default)
	{
		return $this->getAttribute('value', $default);
	}

	public function getAction()
	{
		$action = $this->getAttribute('action');

		$actions = $this->fetchVariables($action);

		$var = reset($actions);
		$raw = explode('.', $var);

		$controller = $raw[0];
		$action = $raw[1];

		return \URL::fromRoute($controller, $action);
	}
}

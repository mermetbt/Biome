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

	public function getURL()
	{
		$controller	= $this->getAttribute('controller', NULL);
		$action		= $this->getAttribute('action', NULL);
		$item		= $this->getAttribute('item', NULL);
		$module		= $this->getAttribute('module', NULL);
		$page		= $this->getAttribute('page', NULL);

		if($item)
		{
			$item = $this->fetchValue($item);
		}

		if($page)
		{
			$page = $this->fetchValue($page);
		}

		return \URL::fromRoute($controller, $action, $item, $module, $page);
	}

	public function getAction()
	{
		$action = $this->getAttribute('action');
		$actions = $this->fetchVariables($action);

		if(empty($actions))
		{
			return $this->getURL();
		}

		$var = reset($actions);
		$raw = explode('.', $var);

		$controller = $raw[0];
		$action = $raw[1];

		return \URL::fromRoute($controller, $action);
	}
}

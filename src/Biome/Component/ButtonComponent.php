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
		$label = $this->getAttribute('value', $default);
		if(strncmp($label, '@string/', 8) == 0)
		{
			$value = substr($label, 8);
			return $this->lang->get($value);
		}
		return $label;
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
		$controller = $this->getAttribute('controller', '');
		$action = $this->getAttribute('action', '');
		if(!empty($controller))
		{
			return $this->getURL();
		}

		$actions = $this->fetchVariables($action);

		$var = reset($actions);
		$raw = explode('.', $var);

		$controller = $raw[0];
		if(isset($raw[1]))
		{
			$action = $raw[1];
		}

		return \URL::fromRoute($controller, $action);
	}
}

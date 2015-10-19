<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class PanelComponent extends Component
{
	public function render()
	{
		$this->addClasses('panel panel-default');
		return parent::render();
	}

	public function getTitle()
	{
		if(!isset($this->attributes['title']))
		{
			return '';
		}
		return $this->attributes['title'];
	}
}

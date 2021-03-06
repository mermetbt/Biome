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
		$title = $this->getAttribute('title', '');
		return $this->fetchValue($title);
	}
}

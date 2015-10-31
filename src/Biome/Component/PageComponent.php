<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class PageComponent extends Component
{
	public function render()
	{
		$this->addClasses('tab-pane fade');
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

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
}

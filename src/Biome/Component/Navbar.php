<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class Navbar extends Component
{
	public function render()
	{
		$form = $this->getChildren('form');

		if($form != NULL)
		{
			$form->addClasses('navbar-form');
		}

		return parent::render();
	}
}

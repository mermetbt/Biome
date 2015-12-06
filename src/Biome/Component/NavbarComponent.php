<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class NavbarComponent extends Component
{
	public function render()
	{
		$forms = $this->getChildren('form', -1);

		foreach($forms AS $form)
		{
			$form->addClasses('navbar-form');
		}

		return parent::render();
	}

	public function getLogo()
	{
		$logo = $this->getAttribute('logo', '');
		return $logo;
	}
}

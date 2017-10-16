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

	public function getType()
	{
		$type = $this->getAttribute('type', 'default');
		return $this->fetchValue($type);
	}

	public function isFixedTop()
	{
		$fixed = $this->getAttribute('fixed-top', '1');
		return $fixed == '1';
	}

	public function isExpanded()
	{
		$expanded = $this->getAttribute('expanded', '1');
		return $expanded == '1';
	}
}

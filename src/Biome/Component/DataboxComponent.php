<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class DataboxComponent extends Component
{
	public function getValue()
	{
		$value = $this->getAttribute('value', NULL);
		return $this->fetchValue($value);
	}

	public function getQuantity()
	{
		$quantity = $this->getAttribute('quantity', '&nbsp;');
		return $this->fetchValue($quantity);
	}

	public function getType()
	{
		$type = $this->getAttribute('type', 'default');
		return $this->fetchValue($type);
	}

	public function getName()
	{
		$name = $this->getAttribute('name');
		return $this->fetchValue($name);
	}

	public function getIcon()
	{
		$icon = $this->getAttribute('icon');
		return $this->fetchValue($icon);
	}
}

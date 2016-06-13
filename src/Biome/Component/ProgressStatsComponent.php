<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class ProgressStatsComponent extends Component
{
	public function getValue()
	{
		$value = $this->getAttribute('value', NULL);
		return $this->fetchValue($value);
	}

	public function getTitle()
	{
		$title = $this->getAttribute('title', '');
		return $this->fetchValue($title);
	}

	public function getType()
	{
		$type = $this->getAttribute('type', 'default');
		return $this->fetchValue($type);
	}

	public function getDescription()
	{
		$description = $this->getAttribute('description', '');
		return $this->fetchValue($description);
	}
}

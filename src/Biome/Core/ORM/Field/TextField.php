<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;

class TextField extends AbstractField
{
	protected $size;

	public function __construct($size = 64)
	{
		$this->size = $size;
	}

	public function setSize($size)
	{
		$this->size = $size;
		return $this;
	}

	public function getSize()
	{
		return $this->size;
	}
}

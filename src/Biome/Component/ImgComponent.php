<?php

namespace Biome\Component;

class ImgComponent extends VariableComponent
{
	public function getSrc()
	{
		$src = $this->getAttribute('src', '');
		return $this->fetchValue($src);
	}

	public function getAlt()
	{
		$alt = $this->getAttribute('alt', '');
		return $this->fetchValue($alt);
	}
}

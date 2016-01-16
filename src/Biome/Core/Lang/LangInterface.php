<?php

namespace Biome\Core\Lang;

interface LangInterface
{
	public function get($pattern, array $parameters = array(), $locale = '');
}

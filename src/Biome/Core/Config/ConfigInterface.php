<?php

namespace Biome\Core\Config;

interface ConfigInterface
{
	public function get($attribute, $default_value = NULL);
}

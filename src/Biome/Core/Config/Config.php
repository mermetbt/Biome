<?php

namespace Biome\Core\Config;

use \Biome\Biome;

class Config
{
	public static function get($attribute, $default_value = NULL)
	{
		return Biome::getService('config')->get($attribute, $default_value);
	}
}

<?php

namespace Biome\Core\Utils;

class Resources
{
	public static function __get($type)
	{
		switch($type)
		{
			case 'string':
				$lang = \Biome\Biome::getService('lang');
				return $lang;
			default:
				throw new Exception('Unrecognized type of resources!');
		}
	}
}

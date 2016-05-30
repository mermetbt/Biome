<?php

namespace Biome\Core;

use \Biome\Core\Logger\Logger;

class Autoload
{
	public static function register()
	{
		spl_autoload_register(array('\Biome\Core\ORM\ObjectLoader', 'load'));
		spl_autoload_register(array('self', 'autoload'));
	}

	private static function autoload($class_name)
	{
		self::classloader($class_name, 'commands');
		self::classloader($class_name, 'controllers');
		self::classloader($class_name, 'collections');
	}

	private static function classloader($class_name, $type)
	{
		$dirs = \Biome\Biome::getDirs($type);

		if(empty($dirs))
		{
			return FALSE;
		}

		$filename = '';
		foreach($dirs AS $d)
		{
			if(!file_exists($d))
			{
				continue;
			}

			$files = scandir($d);
			foreach($files AS $f)
			{
				if($f[0] == '.')
				{
					continue;
				}

				if(strtolower($f) == strtolower($class_name) . '.php')
				{
					$filename = $d . '/' . $f;
				}
			}
		}

		if(file_exists($filename))
		{
			Logger::debug('Load class in ' . $filename);
			include_once($filename);
			return TRUE;
		}
		return FALSE;
	}
}

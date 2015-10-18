<?php

namespace Biome\Core\ORM;

class ObjectLoader
{
	public static function load($object_name)
	{
		if(class_exists($object_name))
		{
			return TRUE;
		}

		/**
		 * TODO: Replace this dirty code by an autoload.
		 */
		$dirs = \Biome\Biome::getDirs('models');
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

				if(strtolower($f) == strtolower($object_name) . '.php')
				{
					$filename = $d . '/' . $f;
				}
			}
		}

		if(file_exists($filename))
		{
			include_once($filename);
			return TRUE;
		}

		return FALSE;
	}

	public static function get($object_name, $raw_values = array(), QuerySet $query_set = NULL)
	{
		self::load($object_name);

		if(!class_exists($object_name))
		{
			throw new \Exception('The object ' . $object_name . ' doesn\'t exists!');
		}

		$object = new $object_name($raw_values, $query_set);

		if(!$object instanceof ObjectInterface)
		{
			throw new \Exception('The models ' . $object_name . ' doesn\'t implement the ObjectInterface!');
		}

		return $object;
	}
}

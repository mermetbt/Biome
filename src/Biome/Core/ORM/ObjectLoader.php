<?php

namespace Biome\Core\ORM;

class ObjectLoader
{
	public static function load($object_name)
	{

		/**
		 * TODO: Replace this dirty code by an autoload.
		 */
		$dirs = \Biome\Biome::getDirs('models');
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
				include_once($d . '/' . $f);
			}
		}

		if(!class_exists($object_name))
		{
			throw new \Exception('The object ' . $object_name . ' doesn\'t exists!');
		}

		$object = new $object_name();

		if(!$object instanceof ObjectInterface)
		{
			throw new \Exception('The models ' . $object_name . ' doesn\'t implement the ObjectInterface!');
		}

		return $object;
	}
}

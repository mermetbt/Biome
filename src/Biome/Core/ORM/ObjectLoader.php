<?php

namespace Biome\Core\ORM;

class ObjectLoader
{
	public static function load($object_name)
	{
		if(empty($object_name))
		{
			throw new \Exception('Cannot load an empty object name!');
		}

		if(class_exists($object_name))
		{
			return TRUE;
		}

		$dirs = \Biome\Biome::getDirs('models');
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

	public static function getObjects()
	{
		$modelsDirs = \Biome\Biome::getDirs('models');

		/**
		 * List existings models.
		 */
		$objects_list = array();
		foreach($modelsDirs AS $dir)
		{
			if(!file_exists($dir))
			{
				continue;
			}
			$filenames = scandir($dir);
			foreach($filenames AS $file)
			{
				if($file[0] == '.')
				{
					continue;
				}

				$object_name = substr($file, 0, -4);
				$objects_list[$object_name] = $object_name;
			}
		}

		$objects = array();

		foreach($objects_list AS $object_name)
		{
			$objects[$object_name] = self::get($object_name);
		}

		return $objects;
	}
}

<?php

namespace Biome\Core\ORM;

use \Biome\Core\Logger\Logger;
use \Biome\Core\ORM\Exception\ObjectNotFoundException;

class ObjectLoader
{
	public static function load($object_name)
	{
		if(empty($object_name))
		{
			throw new ObjectNotFoundException('Cannot load an empty object name!');
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
			Logger::debug('Load object ' . $object_name . ' in ' . $filename);
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
			throw new ObjectNotFoundException('The object ' . $object_name . ' doesn\'t exists!');
		}

		if(!is_subclass_of($object_name, '\Biome\Core\ORM\Models'))
		{
			throw new ObjectNotFoundException('The object ' . $object_name . ' is not an instance of Models!');
		}

		$object = new $object_name($raw_values, $query_set);

		if(!$object instanceof ObjectInterface)
		{
			throw new ObjectNotFoundException('The models ' . $object_name . ' doesn\'t implement the ObjectInterface!');
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

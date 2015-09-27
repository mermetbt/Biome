<?php

namespace Biome\Core;

use Biome\Core\ORM\ObjectLoader;

use Serializable;

class Collection implements Serializable
{
	protected $map = array();
	protected $_instances = array();
	private static $_collections_set = array();

	public static function get($collection_name = NULL)
	{
		if($collection_name === NULL)
		{
			// Retrieve the current collection.
		}

		if(!empty(self::$_collections_set[$collection_name]))
		{
			return self::$_collections_set[$collection_name];
		}

		$class_name = $collection_name . 'Collection';

		/**
		 * TODO: Replace this dirty code by an autoload.
		 */
		$dirs = array(
			__DIR__ . '/../../app/collections/',
			$dir =  \Biome\Biome::getDir('collections')
		);

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

		if(!class_exists($class_name))
		{
			return NULL;
		}

		$c = new $class_name();

		self::$_collections_set[$collection_name] = $c;

		return $c;
	}

	public function serialize()
	{
		return serialize($this->_instances);
	}

	public function unserialize($serialized)
	{
		foreach($this->map AS $name => $class_name)
		{
			$this->$name;
		}

		$this->_instances = unserialize($serialized);
	}

	public function __set($attribute_name, $value)
	{
		if(!isset($this->map[$attribute_name]))
		{
			throw new \Exception('No mapping defined in collection for attribute ' . $attribute_name . '!');
		}
		$this->_instances[$attribute_name] = $value;
	}

	public function __get($attribute_name)
	{
		if(!isset($this->map[$attribute_name]))
		{
			throw new \Exception('No mapping defined in collection for attribute ' . $attribute_name . '!');
		}

		if(!isset($this->_instances[$attribute_name]))
		{
			if(is_string($this->map[$attribute_name]))
			{
				$this->_instances[$attribute_name] = ObjectLoader::load($this->map[$attribute_name]);
			}
			else
			{
				$this->_instances[$attribute_name] = $this->map[$attribute_name];
			}
		}
		return $this->_instances[$attribute_name];
	}
}

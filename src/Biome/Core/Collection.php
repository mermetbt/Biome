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
			$class_name = get_called_class();
		}
		else
		{
			$class_name = $collection_name . 'Collection';
		}

		$class_name = strtolower($class_name);

		if(!empty(self::$_collections_set[$class_name]))
		{
			return self::$_collections_set[$class_name];
		}

		if(!class_exists($class_name))
		{
			return NULL;
		}

		$c = new $class_name();

		self::$_collections_set[$class_name] = $c;

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
			throw new \Exception('No mapping defined in collection ' . get_class() . ' for attribute ' . $attribute_name . '!');
		}

		if(!isset($this->_instances[$attribute_name]))
		{
			if(is_string($this->map[$attribute_name]))
			{
				$this->_instances[$attribute_name] = ObjectLoader::get($this->map[$attribute_name]);
			}
			else
			{
				$this->_instances[$attribute_name] = $this->map[$attribute_name];
			}
		}
		return $this->_instances[$attribute_name];
	}
}

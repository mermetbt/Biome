<?php

namespace Biome\Core;

class Rights
{
	protected $rights = array();

	private function __construct(array $rights = array())
	{
		$this->rights = $rights;
	}

	public static function loadFromJSON($rights)
	{
		if(empty($rights))
		{
			return new Rights();
		}

		$rights_array = (array)json_decode($rights, TRUE);

		return new Rights($rights_array);
	}

	public static function loadFromArray(array $rights)
	{
		if(empty($rights))
		{
			return new Rights();
		}

		return new Rights($rights);
	}

	public function exportToJSON()
	{
		return json_encode($this->rights);
	}

	public function setRoute($method, $controller, $action, $allowed = TRUE)
	{
		$this->rights['routes'][$method][$controller][$action]	= ($allowed) ? '1' : '';
		return $this;
	}

	public function setObject($object_name, $view = TRUE, $create = TRUE, $edit = TRUE, $delete = TRUE)
	{
		$this->rights['objects'][$object_name]['object']['view']	= ($view) ? '1' : '';
		$this->rights['objects'][$object_name]['object']['create']	= ($create) ? '1' : '';
		$this->rights['objects'][$object_name]['object']['edit']	= ($edit) ? '1' : '';
		$this->rights['objects'][$object_name]['object']['delete']	= ($delete) ? '1' : '';
		return $this;
	}

	public function setAttribute($object_name, $attribute_name, $view = TRUE, $edit = TRUE)
	{
		$this->rights['objects'][$object_name]['attributes'][$attribute_name]['view'] = ($view) ? '1' : '';
		$this->rights['objects'][$object_name]['attributes'][$attribute_name]['edit'] = ($edit) ? '1' : '';
		return $this;
	}

	public function isRouteAllowed($method, $controller, $action)
	{
		return !empty($this->rights['routes'][$method][$controller][$action]);
	}

	public function isUrlAllowed($method, $url)
	{
		$base = URL::getBaseURL();
		$url  = substr($url, strlen($base) + 1);

		$raw = explode('/', $url);

		$controller = empty($raw[0]) ? 'index' : $raw[0];
		$action = empty($raw[2]) ? (empty($raw[1]) ? 'index' : $raw[1]) : $raw[2];

		return $this->isRouteAllowed($method, $controller, $action);
	}

	public function isObjectView($object_name)
	{
		if(empty($object_name))
		{
			throw new \Exception('Object name cannot be empty!');
		}
		return !empty($this->rights['objects'][$object_name]['object']['view']);
	}

	public function isObjectEdit($object_name)
	{
		if(empty($object_name))
		{
			throw new \Exception('Object name cannot be empty!');
		}
		return !empty($this->rights['objects'][$object_name]['object']['edit']);
	}

	public function isObjectCreate($object_name)
	{
		if(empty($object_name))
		{
			throw new \Exception('Object name cannot be empty!');
		}
		return !empty($this->rights['objects'][$object_name]['object']['create']);
	}

	public function isObjectDelete($object_name)
	{
		if(empty($object_name))
		{
			throw new \Exception('Object name cannot be empty!');
		}
		return !empty($this->rights['objects'][$object_name]['object']['delete']);
	}

	public function isAttributeView($object_name, $attribute_name = '')
	{
		if(empty($object_name))
		{
			throw new \Exception('Object name cannot be empty!');
		}

		if($object_name instanceof \Biome\Core\ORM\AbstractField)
		{
			$attribute_name = $object_name->getName();
			$object_name = $object_name->getModel();
		}
		else
		if(empty($attribute_name))
		{
			throw new \Exception('Attribute name cannot be empty!');
		}

		return !empty($this->rights['objects'][$object_name]['attributes'][$attribute_name]['view']);
	}

	public function isAttributeEdit($object_name, $attribute_name = '')
	{
		if(empty($object_name))
		{
			throw new \Exception('Object name cannot be empty!');
		}

		if($object_name instanceof \Biome\Core\ORM\AbstractField)
		{
			$attribute_name = $object_name->getName();
			$object_name = $object_name->getModel();
		}
		else
		if(empty($attribute_name))
		{
			throw new \Exception('Attribute name cannot be empty!');
		}

		return !empty($this->rights['objects'][$object_name]['attributes'][$attribute_name]['edit']);
	}
}

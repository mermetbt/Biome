<?php

namespace Biome\Core;

use Symfony\Component\HttpFoundation\Response;
use Sabre\Xml\Reader;

class View
{
	protected $_tree = NULL;

	public function __construct(Response $response)
	{

	}

	public function load($controller, $action)
	{
		$path = __DIR__ . '/../../app/views/' . $controller . '.xml';
		if(!file_exists($path))
		{
			return FALSE;
		}

		$xml_contents = file_get_contents($path);
		$reader = new Reader();

		$components = scandir(__DIR__ . '/../Component/');
		$components_list = array();
		foreach($components AS $file)
		{
			if($file[0] == '.')
			{
				continue;
			}

			if(substr($file, -4) != '.php')
			{
				continue;
			}

			$componentName = substr($file, 0, -4);
			$components_list['{http://github.com/mermetbt/Biome/}' . strtolower($componentName)] = 'Biome\\Component\\'.$componentName;
		}

		$reader->elementMap = $components_list;

		$reader->xml($xml_contents);
		$tree = $reader->parse();

		if($tree['value'] instanceof \Biome\Component\Views)
		{
			$node = $tree['value'];
			$node->load($action);
			$this->_tree = $node;
		}
	}

	public function render()
	{
		if($this->_tree == NULL)
		{
			return FALSE;
		}
		return $this->_tree->render();
	}
}

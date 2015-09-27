<?php

namespace Biome\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sabre\Xml\Reader;

class View
{
	protected $_tree = NULL;
	protected $_variables = array();

	protected $_request = NULL;
	protected $_response = NULL;

	protected $_title = 'Biome';

	public function __construct(Request $request, Response $response)
	{
		$this->_request = $request;
		$this->_response = $response;
	}

	public function __set($variable, $value)
	{
		$this->_variables[$variable] = $value;
	}

	public function __get($variable)
	{
		if(!isset($this->_variables[$variable]))
		{
			return NULL;
		}
		return $this->_variables[$variable];
	}

	public function load($controller, $action)
	{
		/**
		 * Opening template file
		 */
		$path = \Biome\Biome::getDir('views') . '/' . $controller .'.xml';
		if(!file_exists($path))
		{
			$path = __DIR__ . '/../../app/views/' . $controller . '.xml';
			if(!file_exists($path))
			{
				return FALSE;
			}
		}

		$xml_contents = file_get_contents($path);
		$reader = new Reader();

		/**
		 * Loading components
		 */
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

		/**
		 * Parsing XML template
		 */

		$reader->xml($xml_contents);
		$tree = $reader->parse();

		View\Component::$view = $this;

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

	public function setTitle($title)
	{
		$this->_title = $title;
	}

	public function getTitle()
	{
		return $this->_title;
	}

	public function getBaseUrl()
	{
		return $this->_request->getBaseUrl();
	}
}

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

		$reader->elementMap = array(
			'{http://github.com/mermetbt/Biome/}views' => 'Biome\Component\Views',
			'{http://github.com/mermetbt/Biome/}view' => 'Biome\Component\View',
		);

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

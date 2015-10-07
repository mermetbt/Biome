<?php

namespace Biome\Core;

use Biome\Core\View\TemplateReader;

class View
{
	private $_view_state = NULL;

	protected $_tree = NULL;
	protected $_variables = array();

	protected $_title = 'Biome';

	public function __construct()
	{
		$this->_view_state = md5(rand());
	}

	public function getViewState()
	{
		return $this->_view_state;
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
		$tree = TemplateReader::loadFilename($path);

		View\Component::$view = $this;

		if($tree['value'] instanceof \Biome\Component\ViewsComponent)
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
}

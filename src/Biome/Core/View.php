<?php

namespace Biome\Core;

use Biome\Core\View\TemplateReader;

class View
{
	private $_view_state = NULL;

	protected $_tree = NULL;
	protected $_variables = array();

	protected $_title = 'Biome';
	protected $_rawJavascript = array();

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
		$dirs = \Biome\Biome::getDirs('views');
		$template_file = '';
		foreach($dirs AS $dir)
		{
			$path = $dir . '/' . $controller .'.xml';
			if(!file_exists($path))
			{
				continue;
			}
			$template_file = $path;
		}

		if(!file_exists($template_file))
		{
			throw new \Biome\Core\View\Exception\TemplateNotFoundException('Missing template file for ' . $controller . '->' . $action);
		}

		$tree = TemplateReader::loadFilename($template_file);

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

	public function ajaxHandle($node_id)
	{
		return $this->_tree->ajaxHandle($node_id);
	}

	/**
	 * Meta-informations of the page.
	 */
	public function setTitle($title)
	{
		$this->_title = $title;
	}

	public function getTitle()
	{
		return $this->_title;
	}

	/**
	 * Javascript management.
	 */
	public function javascript($func)
	{
		$this->_rawJavascript[] = $func;
		return $this;
	}

	public function printJavascript()
	{
		foreach($this->_rawJavascript AS $func)
		{
			if(is_callable($func))
			{
				$func();
			}
			else
			{
				echo $func;
			}
		}
	}
}

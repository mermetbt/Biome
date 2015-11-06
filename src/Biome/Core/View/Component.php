<?php

namespace Biome\Core\View;

use Biome\Core\HTTP\Request;

class Component extends NodeLoader
{
	protected	$id				= '';
	public		$_fullname		= '';
	public		$_nodename		= '';
	public		$_attributes	= array();
	protected	$_value			= array();
	protected	$_parent		= NULL;
	public		$classes		= array();

	public static $view	= NULL;

	use ContextManager;

	const DEFAULT_VALUE = '6f66b0b7c0933911e75dcd061cc72f2db2a8fecc3f582a305f66b90c7b674f8754a695761b48004189ee7c608c1408de';

	protected static $_counter = 1;

	/**
	 *
	 * Components dependencies injection.
	 *
	 */
	public function __get($varname)
	{
		return \Biome\Biome::getService($varname);
	}

	/**
	 *
	 * Components rendering
	 *
	 */

	public function render()
	{
		$content = $this->renderComponent();
		return $content;
	}

	public function renderComponent()
	{
		$component_template_file = __DIR__ . '/../../Component/templates/' . $this->_nodename . '.php';
		if(file_exists($component_template_file))
		{
			ob_start();
			$view = self::$view;
			include($component_template_file);
			$content = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			throw new \Exception('Template file not found: ' . $component_template_file . ' for component ' . get_called_class() . ' - ' . $this->_fullname);
		}
		return $content;
	}

	public function getContent($child_name = NULL, array $attribute = NULL)
	{
		ob_start();
		$this->rec_renderChildren($this->_value);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	private function rec_renderChildren(array $nodes)
	{
		/* Render childs components first. */
		foreach($nodes AS $v)
		{
			if($v instanceof Component)
			{
				echo $v->render();
			}
			else
			// Standard HTML
			if(is_array($v))
			{
				if(strncmp($v['name'], '{}', 2) != 0)
				{
					throw new \Exception('Unrecognized component namespace ' . $v['name']);
				}

				$markup = preg_replace('/{(.*)}/', '', $v['name']);

				if($markup == 'br')
				{
					echo '<br/>';
					continue;
				}

				echo '<', $markup;
				if(!empty($v['attributes']))
				{
					$attributes = array();
					foreach($v['attributes'] AS $attr => $value)
					{
						$attributes[] = $attr . '="' . $value . '"';
					}
					echo ' ', join(' ', $attributes);
				}
				echo '>';

				if(is_array($v['value']))
				{
					$this->rec_renderChildren($v['value']);
				}
				else
				{
					echo $v['value'];
				}

				echo '</', $markup, '>';
			}
			else
			{
				echo $v;
			}
		}
		return TRUE;
	}

	/**
	 * AJAX Handling.
	 */
	public function ajaxHandle($node_id)
	{
		return $this->rec_ajaxHandle($this->_value, $node_id);
	}

	protected function rec_ajaxHandle($nodes, $node_id)
	{
		foreach($nodes AS $node)
		{
			if($node instanceof Component)
			{
				if($node->getId() == $node_id)
				{
					$request = $this->request;
					$node->handleAjaxRequest($request);
					return TRUE;
				}

				if($node->ajaxHandle($node_id))
				{
					return TRUE;
				}
			}
			else
			if(is_array($node))
			{
				if(is_array($node['value']))
				{
					if($this->rec_ajaxHandle($node['value'], $node_id))
					{
						return TRUE;
					}
				}
			}
		}

		return FALSE;
	}

	public function handleAjaxRequest(Request $request) { }

	/**
	 *
	 * Component subcomponents management.
	 *
	 */
	public function getChilds()
	{
		return $this->_value;
	}

	public function getChildren($component_name = NULL, $level = 0, &$children = array())
	{
		if($level > 0)
		{
			$sub_level = $level - 1;
		}

		foreach($this->getChilds() AS $c)
		{
			if(!($c instanceof Component))
			{
				continue;
			}

			if($component_name === NULL || $c->getNodeName() == $component_name)
			{
				$children[] = $c;
			}

			if($level !== 0)
			{
				$c->getChildren($component_name, $level-1, $children);
			}
		}

		return $children;
	}

	public function getParent($component_name = NULL)
	{
		if($this->_parent === NULL)
		{
			return NULL;
		}

		if($component_name === NULL)
		{
			return $this->_parent;
		}

		if($this->_parent->getNodeName() == $component_name)
		{
			return $this->_parent;
		}

		return $this->_parent->getParent($component_name);
	}

	/**
	 *
	 * Component attributes management.
	 *
	 */

	public function getId()
	{
		if(!empty($this->id))
		{
			return $this->id;
		}

		if(!isset($this->_attributes['id']))
		{
			$this->id = 'biome_' . (self::$_counter++);
			return $this->id;
		}

		$this->id = $this->_attributes['id'];
		return $this->id;
	}

	public function getNodeName()
	{
		return $this->_nodename;
	}

	public function getClasses()
	{
		if(isset($this->_attributes['class']))
		{
			$this->addClasses($this->_attributes['class']);
		}
		return join(' ', $this->classes);
	}

	public function addClasses($css_class)
	{
		$classes = explode(' ', $css_class);
		foreach($classes AS $c)
		{
			$c = trim($c);
			$this->classes[$c] = $c;
		}
		return TRUE;
	}

	public function removeClasses($css_class)
	{
		$classes = explode(' ', $css_class);
		foreach($classes AS $c)
		{
			$c = trim($c);
			unset($this->classes[$c]);
		}
		return TRUE;
	}

	public function getAttribute($attribute_name, $default_value = Component::DEFAULT_VALUE)
	{
		if(isset($this->_attributes[$attribute_name]))
		{
			return $this->_attributes[$attribute_name];
		}

		if($default_value === Component::DEFAULT_VALUE)
		{
			throw new \Exception('The component ' . $this->getNodeName() . ' needs the attribute: ' . $attribute_name. '!');
		}

		if(is_callable($default_value))
		{
			return $default_value();
		}

		return $default_value;
	}
}

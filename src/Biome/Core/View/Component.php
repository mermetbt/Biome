<?php

namespace Biome\Core\View;

class Component extends NodeLoader
{
	protected	$id			= '';
	public		$fullname	= '';
	public		$name		= '';
	public		$attributes	= array();
	public		$classes	= array();
	protected	$value		= array();

	public static $view	= NULL;

	use ContextManager;

	/**
	 *
	 * Components rendering
	 *
	 */

	public function render()
	{
		$content = $this->renderChildren();
		$content = $this->renderComponent($content);
		return $content;
	}

	public function renderComponent($content)
	{
		$component_template_file = __DIR__ . '/../../Component/templates/' . $this->name . '.php';
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
			throw new \Exception('Template file not found: ' . $component_template_file . ' for component ' . get_called_class() . ' - ' . $this->fullname);
		}
		return $content;
	}

	public function renderChildren()
	{
		ob_start();

		$this->rec_renderChildren($this->value);

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
	 *
	 * Component subcomponents management.
	 *
	 */
	public function getChilds()
	{
		return $this->value;
	}

	public function getChildren($component_name)
	{
		foreach($this->getChilds() AS $c)
		{
			if($c->name == $component_name)
			{
				return $c;
			}
		}
		return NULL;
	}

	/**
	 *
	 * Component attributes management.
	 *
	 */

	public function getId()
	{
		if(!isset($this->attributes['id']))
		{
			$this->id = md5(rand());
			return $this->id;
		}
		$this->id = $this->attributes['id'];
		return $this->id;
	}

	public function getClasses()
	{
		if(isset($this->attributes['class']))
		{
			$this->addClasses($this->attributes['class']);
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
}

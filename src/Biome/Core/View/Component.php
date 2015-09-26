<?php

namespace Biome\Core\View;

use Biome\Core\Collection;

use Sabre\Xml\Element;
use Sabre\Xml\Reader;
use Sabre\Xml\Writer;

class Component implements Element
{
	protected $id		= '';
	public $fullname	= '';
	public $name		= '';
	public $attributes	= array();
	protected $value	= array();
	public static $view	= NULL;

	public static function xmlDeserialize(Reader $reader)
	{
		$class_name = get_called_class();
		$component = new $class_name();

		/* First settings. */
		$component->name = 'views';

		/* Iterate through children. */
		$children = $reader->parseInnerTree();
		if(is_array($children))
		foreach($children as $child)
		{
			/* Load component from the framework. */
			if($child['value'] instanceof Component)
			{
				$child['value']->fullname	= $child['name'];
				$child['value']->name		= strtolower(substr(get_class($child['value']), 16));
				$child['value']->attributes = $child['attributes'];
				$component->value[] = $child['value'];
			}
			/* Load standard HTML markup. */
			else
			{
				$component->value[] = $child;
			}
		}

		return $component;
	}

	public function xmlSerialize(Writer $writer) {}

	public function render()
	{
		$content = $this->renderChildren();

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
		/* Render childs components first. */
		foreach($this->value AS $v)
		{
			if($v instanceof Component)
			{
				echo $v->render();
			}
			else
			if(is_array($v))
			{
				if(strncmp($v['name'], '{}', 2) != 0)
				{
					continue;
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

				echo $v['value'];
				echo '</', $markup, '>';
			}
			else
			{
				echo $v;
			}

		}
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

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

	public function fetchVariable($value)
	{
		$matches = array();
		preg_match('/#{(.*)}/', $value, $matches);

		if(!isset($matches[1]))
		{
			return $value;
		}

		return $matches[1];
	}

	public function fetchValue($value)
	{
		$var = $this->fetchVariable($value);

		if(empty($var))
		{
			return $value;
		}

		$raw = explode('.', $var);

		$collection = $raw[0];
		$object = $raw[1];
		$field = $raw[2];

		$c = Collection::get($collection);
		$o = $c->$object;
		$f = $o->$field;

		return $f;
	}
}

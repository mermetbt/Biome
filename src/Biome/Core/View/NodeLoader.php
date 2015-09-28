<?php

namespace Biome\Core\View;

use Sabre\Xml\Element;
use Sabre\Xml\Reader;
use Sabre\Xml\Writer;

class NodeLoader implements Element
{
	/**
	 *
	 * Component loading and tree construction.
	 *
	 */

	public static function xmlDeserialize(Reader $reader)
	{
		$class_name = get_called_class();
		$component = new $class_name();

		/* First settings. */
		$component->name = 'views';

		/* Iterate through children. */
		$children = $reader->parseInnerTree();

		if(!is_array($children))
		{
			return $component;
		}

		foreach($children as $child)
		{
			$component->value[] = self::rec_xmlDeserialize($child);
		}

		return $component;
	}

	private static function rec_xmlDeserialize($child)
	{
		/* Load component from the framework. */
		if($child['value'] instanceof Component)
		{
			$child['value']->fullname	= $child['name'];
			$child['value']->name		= strtolower(substr(get_class($child['value']), 16));
			$child['value']->attributes = $child['attributes'];
			return $child['value'];
		}

		/* Load standard HTML markup. */
		if(is_array($child['value']))
		{
			$list = array();
			foreach($child['value'] AS $c)
			{
				$list[] = self::rec_xmlDeserialize($c);
			}
			$child['value'] = $list;
			return $child;
		}

		return $child;
	}

	public function xmlSerialize(Writer $writer) {}

}

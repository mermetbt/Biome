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
		$component->_nodename = 'views';

		/* Iterate through children. */
		$children = $reader->parseInnerTree();

		if(!is_array($children))
		{
			return $component;
		}

		foreach($children as $child)
		{
			$component->_value[] = self::rec_xmlDeserialize($child, $component);
		}

		return $component;
	}

	private static function rec_xmlDeserialize($child, $parent_node)
	{
		/* Load component from the framework. */
		if($child['value'] instanceof Component)
		{
			$child['value']->_parent	= $parent_node;
			$child['value']->_fullname	= $child['name'];
			$child['value']->_nodename		= strtolower(substr(get_class($child['value']), strlen('Biome\\Component\\'), -strlen('Component')));
			$child['value']->_attributes = $child['attributes'];
			$child['value']->getId(); // Generate ID.
			$child['value']->building();
			return $child['value'];
		}

		/* Load standard HTML markup. */
		if(is_array($child['value']))
		{
			$list = array();
			foreach($child['value'] AS $c)
			{
				$list[] = self::rec_xmlDeserialize($c, $parent_node);
			}
			$child['value'] = $list;
			return $child;
		}

		return $child;
	}

	public function xmlSerialize(Writer $writer) {}

	public function building() { return TRUE; }

}

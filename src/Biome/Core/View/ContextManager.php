<?php

namespace Biome\Core\View;

use Biome\Core\Collection;
use Biome\Core\ORM\ObjectLoader;
use Biome\Core\ORM\Models;

trait ContextManager
{
	protected static $_context = array(
		'local' => array(),
		'global' => array(),
	);

	public function setContext($var, $value, $context = 'local')
	{
		self::$_context[$context][$var] = $value;
		return TRUE;
	}

	public function getContext($var, &$context = NULL)
	{
		if($context !== NULL)
		{
			if(isset(self::$_context[$context][$var]))
			{
				return self::$_context[$context][$var];
			}
			return NULL;
		}

		if(isset(self::$_context['local'][$var]))
		{
			$context = 'local';
			return self::$_context['local'][$var];
		}

		if(isset(self::$_context['global'][$var]))
		{
			$context = 'global';
			return self::$_context['global'][$var];
		}

		return NULL;
	}

	public function unsetContext($var, $context = 'local')
	{
		foreach(self::$_context[$context] AS $k => $v)
		{
			if(strncmp($var, $k, strlen($var)) == 0)
			{
				unset(self::$_context[$context][$k]);
			}
		}
		return TRUE;
	}

	public function printContext()
	{
		foreach(self::$_context AS $k => $v)
		{
			foreach($v AS $var => $value)
			{
				echo $k, ' -> ', $var, '<br/>';
			}
		}
	}

	public function fetchVariables($value)
	{
		$matches = array();
		preg_match_all('/#{([^}]+)}/', $value, $matches);

		$variables = array();
		foreach($matches[0] AS $index => $pattern)
		{
			$variables[$pattern] = $matches[1][$index];
		}

		return $variables;
	}

	/**
	 * From the longest to the smallest.
	 */
	protected function rec_fetchValue($var, &$inner_context = 'global')
	{
		$ctx = NULL;
		$result = $this->getContext($var, $ctx);
		if($result !== NULL)
		{
			return $result;
		}

		/* Remove one item, save the name of the last one. */
		$raw = explode('.', $var);

		if(count($raw) > 1)
		{
			$end = end($raw);
			unset($raw[count($raw)-1]);
			$result = $this->rec_fetchValue(join('.', $raw), $ctx);

			/* We find the preceding item, fetch the next. */
			if(method_exists($result, 'get' . $end))
			{
				$end = 'get' . $end;
				$result = $result->$end();
			}
			else
			if(method_exists($result, $end))
			{
				$result = $result->$end();
			}
			else
			if(is_array($result))
			{
				$result = $result[$end];
			}
			else
			{
				$result = $result->$end;
			}

			$this->setContext($var, $result, $ctx);
			return $result;
		}

		/* No item found, check view. */
		$view = \Biome\Biome::getService('view');
		$result = $view->$raw[0];
		if($result !== NULL)
		{
			return $result;
		}

		/* No item found, check collections. */
		$result = Collection::get($raw[0]);
		if($result !== NULL)
		{
			return $result;
		}

		/* No item found, check objects. */
		$result = ObjectLoader::get($raw[0]);
		if($result !== NULL)
		{
			return $result;
		}

		throw new \Exception('Unable to find the variable ' . $var . ' in the context!');
	}

	public function fetchValue($value)
	{
		if(!is_string($value))
		{
			throw new \Exception('Value is not a string!');
		}

		$variables = $this->fetchVariables($value);

		foreach($variables AS $key => $var)
		{
			$result = $this->rec_fetchValue($var);
			// Replacing in a string
			if(!is_array($result) && !is_object($result))
			{
				if($result === FALSE)
				{
					$result = '0';
				}
				$value = str_replace($key, $result, $value);
			}
			// Return an object or an array
			else
			{
				return $result;
			}
		}

		return $value;
	}

	public function fetchField($value)
	{
		$variables = $this->fetchVariables($value);

		$field_object = NULL;
		foreach($variables AS $key => $var)
		{
			$raw = explode('.', $var);
			$field = end($raw);
			unset($raw[count($raw)-1]);

			$object = $this->rec_fetchValue(join('.', $raw));
			if(!$object instanceof Models)
			{
				return NULL;
			}

			if($object->hasField($field))
			{
				$field_object = $object->getField($field);
			}
		}

		return $field_object;
	}
}

<?php

namespace Biome\Core\View;

use Biome\Core\Collection;
use Biome\Core\ORM\ObjectLoader;

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
			{
				$result = $result->$end;
			}

			$this->setContext($var, $result, $ctx);
			return $result;
		}

		/* No item found, check collections. */
		$result = Collection::get($raw[0]);
		if($result !== NULL)
		{
			return $result;
		}

		$result = ObjectLoader::load($raw[0]);
		if($result !== NULL)
		{
			return $result;
		}

		throw new \Exception('Unable to find the variable ' . $var . ' in the context!');
	}

	public function fetchValue($value)
	{
		$var = $this->fetchVariable($value);

		if(empty($var))
		{
			return $value;
		}

		$result = $this->rec_fetchValue($var);
		return $result;
	}

	public function fetchType($value)
	{
		$var = $this->fetchVariable($value);

		if(empty($var))
		{
			return $value;
		}

		$raw = explode('.', $var);
		$field = end($raw);
		unset($raw[count($raw)-1]);

		$object = $this->rec_fetchValue(join('.', $raw));
		$type = $object->getFieldType($field);

		return $type;
	}

}

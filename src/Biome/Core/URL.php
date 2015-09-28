<?php

namespace Biome\Core;

use Biome\Core\HTTP\Request;

class URL
{
	protected static $request = NULL;

	public static function getRequest()
	{
		self::$request = Request::createFromGlobals();

		return self::$request;
	}

	public static function getBaseURL()
	{
		return self::$request->getBaseUrl();
	}

	public static function getAsset($asset_path)
	{
		return self::getBaseURL() . '/' . $asset_path;
	}

	public static function fromRoute($controller = NULL, $action = NULL, $item = NULL, $module = NULL)
	{
		$url = self::getBaseURL();

		if($module)
		{
			$url .= '/' . $module;
		}

		if($controller)
		{
			$url .= '/' . $controller;
		}

		if($action)
		{
			$url .= '/' . $action;
		}

		if($item)
		{
			$url .= '/' . $item;
		}

		return $url;
	}
}

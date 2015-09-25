<?php

namespace Biome\Core;

use Symfony\Component\HttpFoundation\Request;

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

	public static function fromRoute($controller, $action = NULL, $item = NULL, $module = NULL)
	{

	}
}

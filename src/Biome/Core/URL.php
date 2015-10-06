<?php

namespace Biome\Core;

class URL
{
	public static function getRequest()
	{
		return \Biome\Biome::getService('request');
	}

	public static function getBaseURL()
	{
		return self::getRequest()->getBaseUrl();
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

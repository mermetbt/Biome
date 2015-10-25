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

	public static function getUri()
	{
		return self::getRequest()->getUri();
	}

	public static function fromRoute($controller = NULL, $action = NULL, $item = NULL, $module = NULL, $page = NULL)
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

		if($item)
		{
			$url .= '/' . $item;
		}

		if($action)
		{
			$url .= '/' . $action;
		}

		if($page)
		{
			$url .= '/' . $page;
		}

		return $url;
	}
}

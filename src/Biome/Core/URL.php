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

	public static function getRoute()
	{
		$request = self::getRequest();
		$pathInfo = $request->getPathInfo();
		return $pathInfo;
	}

	public static function matchRequest($url)
	{
		return $url == self::getBaseURL() . self::getRoute();
	}

	public static function fromRoute($controller = NULL, $action = NULL, $item = NULL, $module = NULL, $page = NULL)
	{
		$url = '';
		if($module)
		{
			$url .= '/' . $module;
		}

		if($controller)
		{
			$url .= '/' . strtolower($controller);
		}

		if($item)
		{
			$url .= '/' . $item;
		}

		if($action)
		{
			$url .= '/' . strtolower($action);
		}

		if($page)
		{
			$url .= '/' . $page;
		}

		$base_url = self::getBaseURL();
		if(empty($base_url))
		{
			$base_url = '/';
		}

		return $base_url . $url;
	}
}

<?php

namespace Biome\Core;

class Error
{
	public static function init()
	{
		error_reporting(E_ALL);
		$whoops = new \Whoops\Run;

		$request = \Biome\Biome::getService('request');
		if($request->acceptHtml())
		{
			$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
		}
		else
		if(\Whoops\Util\Misc::isCommandLine())
		{
			$whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
		}
		else
		{
			$whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler);
		}

		$whoops->register();
	}

}


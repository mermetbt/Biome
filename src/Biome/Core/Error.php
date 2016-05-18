<?php

namespace Biome\Core;

class Error
{
	public static function init()
	{
		error_reporting(E_ALL);
		$whoops = new \Whoops\Run;
		if (\Whoops\Util\Misc::isAjaxRequest())
		{
			$whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler);
		}
		else
		if(\Whoops\Util\Misc::isCommandLine())
		{
			$whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
		}
		else
		{
			$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
		}
		$whoops->register();
	}

}


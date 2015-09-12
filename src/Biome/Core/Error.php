<?php

namespace Biome\Core;

class Error
{
	public static function init()
	{
		error_reporting(E_ALL);
		$whoops = new \Whoops\Run;
		$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
		$whoops->register();
	}

}


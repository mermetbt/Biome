<?php

namespace Biome\Core;

use \Biome\Core\Error\ErrorInterface;

class Error
{
	private static $_handler = NULL;

	public static function setHandler(ErrorInterface $handler)
	{
		self::$_handler = $handler;
	}

	public static function init()
	{
		error_reporting(E_ALL);

		if(self::$_handler instanceof ErrorInterface)
		{
			self::$_handler->init();
		}
	}
}

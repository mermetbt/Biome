<?php
namespace Biome;

use Biome\Core\Route;
use Biome\Core\Error;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Biome
{
	protected static $directories = array();

	public static function start()
	{
		/* Initializing the Framework. */
		Error::init();

		$request = Request::createFromGlobals();

		/* Routing. */
		$router = new Route($request, array(__DIR__ . '/../app/controllers/', self::getDir('controllers')));
		$router->autoroute();

		/* Dispatch. */
		$dispatcher = $router->getDispatcher();
		$response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

		/* Send the response. */
		$response->send();

		/* Commit. */
	}

	public static function registerDirs(array $dirs)
	{
		self::$directories = $dirs;
	}

	public static function getDir($type)
	{
		return self::$directories[$type];
	}
}

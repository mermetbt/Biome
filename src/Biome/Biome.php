<?php
namespace Biome;

use Biome\Core\Route;
use Biome\Core\Error;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Biome
{
	public static function start()
	{
		/* Initializing the Framework. */
		Error::init();

		$request = Request::createFromGlobals();

		/* Routing. */
		$router = new Route($request, array(__DIR__ . '/../app/controllers/'));
		$router->autoroute();

		/* Dispatch. */
		$dispatcher = $router->getDispatcher();
		$response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

		/* Send the response. */
		$response->send();

		/* Commit. */
	}
}

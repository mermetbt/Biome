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

		/* Routing. */
		$router = new Route();
		$router->autoroute();


		/* Dispatch. */
		$dispatcher = $router->getDispatcher();
		$request = Request::createFromGlobals();
		$response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

		/* Send the response. */
		$response->send();

		/* Commit. */
	}
}

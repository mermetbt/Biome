<?php

use Biome\Core\Command\AbstractCommand;

class RoutesCommand extends AbstractCommand
{
	/**
	 * @description List all the routes.
	 */
	public function listRoutes()
	{
		$router = \Biome\Biome::getService('router');
		$routes = $router->routes_list;
		print_r($routes);
	}
}

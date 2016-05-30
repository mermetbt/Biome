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

		foreach($routes AS $route)
		{
			echo $route['method'], ' ', $route['path'], PHP_EOL;
		}
	}
}

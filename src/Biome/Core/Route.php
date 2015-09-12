<?php

namespace Biome\Core;

use League\Route\RouteCollection;

class Route extends RouteCollection
{
	/**
	 * This method generate all the route available in the controllers.
	 */
	public function autoroute()
	{
		$this->addRoute('GET', '/', function (Request $request, Response $response) {
			$response->setContent('Init;');
		});
	}
}

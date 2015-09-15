<?php

namespace Biome\Core;

use Symfony\Component\HttpFoundation\Response;
use Sabre\Xml\Reader;

class View
{
	public function __construct(Response $response)
	{

	}

	public function load($controller, $action)
	{
		$path = __DIR__ . '/../../app/views/' . $controller . '.xml';
		if(!file_exists($path))
		{
			return FALSE;
		}

		$xml_contents = file_get_contents($path);
		$reader = new Reader();
		$reader->xml($xml_contents);
		$tree = $reader->parse();


	}

	public function render()
	{

	}
}

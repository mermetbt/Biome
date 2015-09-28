<?php

namespace Biome\Core\HTTP;

use \Symfony\Component\HttpFoundation\RedirectResponse;

class Response extends \Symfony\Component\HttpFoundation\Response
{
	public function redirect($controller = NULL, $action = NULL, $item = NULL, $module = NULL)
	{
		$url = \URL::fromRoute($controller, $action, $item, $module);
		return new RedirectResponse($url);
	}
}

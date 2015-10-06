<?php

namespace Biome\Core\HTTP;

use \Symfony\Component\HttpFoundation\RedirectResponse;

class Response extends \Symfony\Component\HttpFoundation\Response
{
	public function redirect($controller = NULL, $action = NULL, $item = NULL, $module = NULL)
	{
		if($controller === NULL)
		{
			$url = \Biome\Biome::getService('request')->headers->get('referer');
		}
		else
		{
			$url = \URL::fromRoute($controller, $action, $item, $module);
		}

		$this->headers->set('Location', $url);
		$this->setStatusCode(302);
		return $this;
	}
}

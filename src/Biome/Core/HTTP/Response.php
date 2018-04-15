<?php

namespace Biome\Core\HTTP;

use \Symfony\Component\HttpFoundation\RedirectResponse;
use Biome\Core\Logger\Logger;

class Response extends \Symfony\Component\HttpFoundation\Response
{
	public function setContentType($content_type)
	{
		$this->headers->set('Content-Type', $content_type);
		return $this;
	}

	public function redirect($controller = NULL, $action = NULL, $item = NULL, $module = NULL)
	{
		if($controller === NULL)
		{
			$url = \Biome\Biome::getService('request')->headers->get('referer');
		}
		else
		if(strpos($controller, '://') !== FALSE)
		{
			$url = $controller;
		}
		else
		{
			$url = \URL::fromRoute($controller, $action, $item, $module);
		}

		Logger::info('Redirect to ' . $url);

		$this->headers->set('Location', $url);
		$this->setStatusCode(302);
		return $this;
	}
}

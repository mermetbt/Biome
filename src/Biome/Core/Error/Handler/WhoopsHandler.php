<?php

namespace Biome\Core\Error\Handler;

use Biome\Core\Error\ErrorInterface;

class WhoopsHandler implements ErrorInterface {

	public function init() {
		$whoops = new \Whoops\Run;

		if(\Whoops\Util\Misc::isCommandLine())
		{
			$whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
		}
		else
		{
			$request = \Biome\Biome::getService('request');
			if($request->acceptHtml())
			{
				$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
			}
			else
			{
				$whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler);
			}
		}

		$whoops->register();
	}
}

<?php

namespace Biome\Core\HTTP;

class Request extends \Symfony\Component\HttpFoundation\Request
{
	public function getEntryPoint()
	{

		return $this->getSchemeAndHttpHost() . $this->getBasePath();
	}

	public function getCanonicPathInfo()
	{
		return str_replace('//', '/', $this->getPathInfo());
	}

	public function getCanonicURI()
	{
		return str_replace('//', '/', $this->getURI());
	}
}

<?php

namespace Biome\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
	protected $request;
	protected $response;

	public function __construct(Request $request, Response $response)
	{
		$this->request	= $request;
		$this->response = $response;
	}

	public function request()
	{
		return $this->request;
	}

	public function response()
	{
		return $this->response;
	}

	public function process($type, $controller_name, $action_name, $method_name)
	{
		if($type == 'GET')
		{
			$this->view = new View($this->request, $this->response);
			$this->view->load($controller_name, $action_name);
		}

		$this->$method_name();

		if($type == 'GET')
		{
			// Render view
			$content = $this->view->render();
			if(!empty($content))
			{
				$this->response->setContent($content);
			}
		}

		return $this->response;
	}
}

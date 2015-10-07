<?php

namespace Biome\Core;

use Biome\Core\HTTP\Request;
use Biome\Core\HTTP\Response;

use Biome\Core\View\Flash;

class Controller
{
	protected $request;
	protected $response;

	public function __construct(Request $request, Response $response)
	{
		$this->request	= $request;
		$this->response = $response;
	}

	public function flash()
	{
		return Flash::getInstance();
	}

	public function request()
	{
		return $this->request;
	}

	public function response()
	{
		return $this->response;
	}

	protected function preRoute() { return TRUE; }

	protected function postRoute(Response $response) { return $response; }

	public function process($type, $controller_name, $action_name, $method_name, $method_params)
	{
		/**
		 * preRoute
		 */
		if(!$this->preRoute())
		{
			return $this->response();
		}

		if($type == 'GET')
		{
			$this->view = \Biome\Biome::getService('view');
			$this->view->load($controller_name, $action_name);
		}

		$result = call_user_func_array(array($this, $method_name), $method_params);

		if($result instanceof Response)
		{
			$this->response = $result;
		}

		if($type == 'GET' && !$this->response->isRedirection())
		{
			// Render view
			$content = $this->view->render();
			if(!empty($content))
			{
				$this->response->setContent($content);
			}
		}

		$this->response = $this->postRoute($this->response);

		return $this->response;
	}
}

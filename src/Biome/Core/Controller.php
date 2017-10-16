<?php

namespace Biome\Core;

use Biome\Core\HTTP\Request;
use Biome\Core\HTTP\Response;

use Biome\Core\View\Flash;

use Biome\Core\Logger\Logger;

use League\Route\Http\Exception\ForbiddenException as ForbiddenException;

class Controller
{
	protected $request;
	protected $response;

	private $_call_params	= array();

	public function __construct(Request $request, Response $response)
	{
		Logger::info('Instanciation of router ' . get_called_class());
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

	protected function beforeRoute() { return TRUE; }

	protected function afterRoute(Response $response) { return $response; }

	protected function checkAuthorizations()
	{
		$type				= $this->_call_params['http_method_type'];
		$controller_name	= $this->_call_params['controller_name'];
		$action_name		= $this->_call_params['action_name'];

		$rights = \Biome\Biome::getService('rights');

		if(!$rights->isRouteAllowed($type, $controller_name, $action_name))
		{
			throw new ForbiddenException('Route ' . $type . ' /' . $controller_name . '/' . $action_name . ' unallowed!');
		}
		return TRUE;
	}

	public function process($type, $controller_name, $action_name, $method_name, $method_params)
	{
		Logger::info('Processing method ' . $method_name);

		$this->_call_params['http_method_type']	= $type;
		$this->_call_params['controller_name']	= $controller_name;
		$this->_call_params['action_name']		= $action_name;
		$this->_call_params['method_name']		= $method_name;
		$this->_call_params['method_params']	= $method_params;

		/**
		 * preRoute
		 */
		if(!$this->beforeRoute())
		{
			Logger::info('Before route returned FALSE => skip action!');
			return $this->response();
		}

		$rendering = ($type == 'GET') ? TRUE : FALSE;

		$this->view = \Biome\Biome::getService('view');
		try
		{
			$this->view->load($controller_name, $action_name);
		}
		catch(\Biome\Core\View\Exception\TemplateNotFoundException $e)
		{
			$rendering = FALSE;
		}

		$result = call_user_func_array(array($this, $method_name), $method_params);

		if($result instanceof Response)
		{
			$this->response = $result;
		}

		// Ajax Request
		if($this->request->isXmlHttpRequest())
		{
			$rendering = FALSE;
			$partial_rendering = $this->request->get('partial');
			if($partial_rendering)
			{
				$this->response->setContentType('application/json');
				$this->view->ajaxHandle($partial_rendering);
			}
		}

		if($rendering && !$this->response->isRedirection())
		{
			// Render view
			$content = $this->view->render();
			if(!empty($content))
			{
				$this->response->setContent($content);
			}
		}

		$this->response = $this->afterRoute($this->response);

		return $this->response;
	}
}

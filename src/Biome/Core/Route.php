<?php

namespace Biome\Core;

use League\Route\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Route extends RouteCollection
{
	protected $_request = NULL;
	protected $_controllers_dir = array();

	public function __construct(Request $request, array $controllers_dir = array())
	{
		parent::__construct();
		$this->_request = $request;
		$this->_controllers_dir = $controllers_dir;
	}

	/**
	 * This method generate all the route available in the controllers.
	 */
	public function autoroute()
	{
		/**
		 * Generating all routes.
		 * TODO: Caching
		 */
		$routes = array();
		foreach($this->_controllers_dir AS $dir)
		{
			$files = scandir($dir);
			foreach($files AS $file)
			{
				if($file[0] == '.')
				{
					continue;
				}

				if(substr($file, -14) != 'Controller.php')
				{
					continue;
				}

				$controller_name = substr($file, 0, -14);
				include_once($dir . '/' . $file);
				$class_name = $controller_name . 'Controller';
				$routes[$controller_name] = $this->getRoutesFromClassName($class_name);
			}
		}

		foreach($routes AS $controller => $actions)
		{
			$controller_name = strtolower($controller);
			foreach($actions AS $type => $action)
			{
				foreach($action AS $name => $meta)
				{
					$route_path = '/';
					if(!($controller_name == 'index' && $name == 'index'))
					{
						$route_path .= $controller_name . '/' . strtolower($name);
					}

					$method = function(Request $request, Response $response) use($type, $controller_name, $name, $meta) {
						/* Initialize the controller. */
						$ctrl = new $meta['controller']($request, $response);

						/* Execute the action. */
						return $ctrl->process($type, $controller_name, $name, $meta['action'], $meta['parameters']);
					};

					$this->addRoute($type, $route_path, $method);
					if($name == 'index')
					{
						$route_path = '/' . $controller_name;
						$this->addRoute($type, $route_path, $method);
					}

					if($controller_name == 'index' && $name == 'index')
					{
						$route_path = '/' . $controller_name . '/' . $name;
						$this->addRoute($type, $route_path, $method);
					}
				}
			}
		}
	}

	protected function getRoutesFromClassName($classname)
	{
		$reflection = new \ReflectionClass($classname);

		$methods = $reflection->getMethods();

		$routes = array();
		foreach($methods AS $method)
		{
			/**
			 * Type of HTTP Method.
			 */

			$m_upper = strtoupper($method->getName());

			// GET
			if(strncmp($m_upper, 'GET', 3) == 0)
			{
				$method_type = 'GET';
				$method_name = substr($method->getName(), 3);
			}
			else
			if(strncmp($m_upper, 'POST', 4) == 0)
			{
				$method_type = 'POST';
				$method_name = substr($method->getName(), 4);
			}
			else
			if(strncmp($m_upper, 'PUT', 3) == 0)
			{
				$method_type = 'PUT';
				$method_name = substr($method->getName(), 3);
			}
			else
			if(strncmp($m_upper, 'DELETE', 6) == 0)
			{
				$method_type = 'DELETE';
				$method_name = substr($method->getName(), 6);
			}
			else
			{
				continue;
			}

			$method_name = strtolower($method_name);

			/**
			 * Parameters
			 */
			$param_list = array();
			$parameters = $method->getParameters();
			foreach($parameters AS $param)
			{
				// PHP 7
				if(method_exists($param, 'getType'))
				{
					$type = $param->getType();
				}
				// PHP 5
				else
				{
					$type = $this->extractParamTypeFromString((string)$param);
				}
				$name = $param->getName();
				$required = !$param->allowsNull();
				$param_list[] = array('type' => $type, 'name' => $name, 'required' => $required);
			}

			/**
			 * Save in routes
			 */
			$routes[$method_type][$method_name] = array(
												'controller' => $method->getDeclaringClass()->getName(),
												'action' => $method->getName(),
												'parameters' => $param_list
			);
		}

		return $routes;
	}

	protected function extractParamTypeFromString($param_str)
	{
		$matches = array();
		preg_match('/\[(.*)\]/', $param_str, $matches);

		$raw = explode(' ', trim($matches[1]));

		$required = ($raw[0] == '<required>') ? TRUE : FALSE;
		$type = ($raw[1][0] == '$') ? '' : $raw[1];

		return $type;
	}
}

<?php

namespace Biome\Core;

use League\Route\RouteCollection;
use League\Container\Container;

use Biome\Core\ORM\ObjectLoader;

use Biome\Core\HTTP\Request;
use Biome\Core\HTTP\Response;

class Route extends RouteCollection
{
	protected $classname_routes = array();

	public $routes_list = array();

	public function __construct()
	{
		$request = \Biome\Biome::getService('request');
		$container = new Container();
		$container->add('Symfony\Component\HttpFoundation\Request', $request);
		$container->add('Symfony\Component\HttpFoundation\Response', 'Biome\Core\HTTP\Response');

		parent::__construct($container);
	}

	/**
	 * This method generate all the route available in the controllers.
	 */
	public function autoroute()
	{
		/**
		 * Generating all routes.
		 */
		$cache = NULL;
		$classname_routes = NULL;
		if(\Biome\Biome::hasService('staticcache'))
		{
			$cache = \Biome\Biome::getService('staticcache');
			$classname_routes = $cache->get('classname_routes');
		}

		if(empty($classname_routes))
		{
			$controllers_dirs = \Biome\Biome::getDirs('controllers');
			$controllers_dirs = array_reverse($controllers_dirs);
			foreach($controllers_dirs AS $dir)
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
					$controller_name = strtolower($controller_name);
					// Skip if already defined!
					if(isset($this->classname_routes[$controller_name]))
					{
						continue;
					}
					$this->classname_routes[$controller_name] = $this->getRoutesFromControllerName($controller_name);
				}
			}

			if(!empty($cache))
			{
				$cache->store('classname_routes', $this->classname_routes);
			}
		}
		else
		{
			$this->classname_routes = $classname_routes;
		}

		foreach($this->classname_routes AS $controller_name => $actions)
		{
			foreach($actions AS $method => $action)
			{
				foreach($action AS $name => $meta)
				{
					$handler = function(Request $request, Response $response, array $args) use($method, $controller_name, $name, $meta) {
						/* Initialize the controller. */
						$ctrl = new $meta['controller']($request, $response);

						$method_params = array();
						foreach($meta['parameters'] AS $param)
						{
							switch(strtolower($param['type']))
							{
								case 'biome\core\orm\models':
									$type_param = $ctrl->objectName();
									break;
								case 'biome\core\collection':
									$type_param = $ctrl->collectionName() . 'Collection';
									break;
								default:
									$type_param = $param['type'];
							}

							$method_params[] = $this->parameterInjection($type_param, $param['name'], $param['required'], $args);
						}

						/* Execute the action. */
						return $ctrl->process($method, $controller_name, $name, $meta['action'], $method_params);
					};

					$route_path = $meta['path'];
					$this->addRoute($method, $route_path, $handler);
					$this->routes_list[] = array('method' => $method, 'path' => $route_path);
					if($name == 'index')
					{
						$route_path = '/' . $controller_name;
						$this->addRoute($method, $route_path, $handler);
						$this->routes_list[] = array('method' => $method, 'path' => $route_path);
					}

					if($controller_name == 'index' && $name == 'index')
					{
						$route_path = '/' . $controller_name . '/' . $name;
						$this->addRoute($method, $route_path, $handler);
						$this->routes_list[] = array('method' => $method, 'path' => $route_path);
					}
				}
			}
		}
	}

	protected function getRoutesFromControllerName($controller_name)
	{
		$classname = $controller_name . 'Controller';
		$reflection = new \ReflectionClass($classname);

		$methods = $reflection->getMethods();

		$routes = array();
		foreach($methods AS $method)
		{
			/**
			 * Skip wrong method
			 */
			if($method->isStatic())
			{
				continue;
			}

			if($method->isConstructor())
			{
				continue;
			}

			if(!$method->isPublic())
			{
				continue;
			}

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
			$url_param_list = array();
			$parameters = $method->getParameters();
			foreach($parameters AS $param)
			{
				// PHP 7
				if(method_exists($param, 'getType'))
				{
					$type = (string)$param->getType();
				}
				// PHP 5
				else
				{
					$type = $this->extractParamTypeFromString((string)$param);
				}

				$name = $param->getName();
				$required = !$param->allowsNull();
				$param_list[$name] = array('type' => $type, 'name' => $name, 'required' => $required);
				if(empty($type))
				{
					$url_param_list[] = $name;
				}
			}

			/**
			 * Retrieve annotations
			 */
			$annotations = $this->parseAnnotations($method->getDocComment());

			/**
			 * Generate route path
			 */
			$route_array = array();
			if(!($controller_name == 'index' && $method_name == 'index'))
			{
				// Controller part
				$route_array[] = $controller_name;

				// Object id part
				if(!empty($url_param_list))
				{
					$p = array_shift($url_param_list);
					$route_array[] = '{' . $p . '}';
				}

				// Action part
				if(strlen($method_name) > 0)
				{
					$route_array[] = strtolower($method_name);
				}

				// Page and others part
				foreach($url_param_list AS $p)
				{
					$route_array[] = '{' . $p . '}';
				}
			}
			$route_path = '/' . join('/', $route_array);

			/**
			 * Save in routes
			 */
			$routes[$method_type][$method_name] = array(
												'path' => $route_path,
												'controller' => $method->getDeclaringClass()->getName(),
												'action' => $method->getName(),
												'url_parameters' => $url_param_list,
												'parameters' => $param_list,
												'annotations' => $annotations
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

	protected function parameterInjection($type, $name, $required, $args)
	{
		$request = \Biome\Biome::getService('request');

		$value = NULL;
		if(empty($type))
		{
			return $args[$name];
		}

		switch($type)
		{
			// Default PHP type
			case 'string':
			case 'int':
				return $value;
				break;
			default: // Class injection

		}

		/**
		 * Collection injection
		 */
		if(substr($type, -strlen('Collection')) == 'Collection')
		{
			// Instanciate the collection
			$collection_name = strtolower(substr($type, 0, -strlen('Collection')));
			$value = Collection::get($collection_name);

			// Check if data are sent
			foreach($request->request->keys() AS $key)
			{
				if(strncmp($collection_name . '/', $key, strlen($collection_name . '/')) == 0)
				{
					$raw = explode('/', $key);
					$total = count($raw);

					$iter = $value;
					for($i = 1; $i < $total-1; $i++)
					{
						$iter = $iter->{$raw[$i]};
					}
					$v = $request->request->get($key);
					$iter->{$raw[$i]} = $v;
				}
			}
		}
		else
		/**
		 * Object injection
		 */
		{
			$object_name = strtolower($type);
			$value = ObjectLoader::get($object_name);

			// Check if data are sent
			foreach($request->request->keys() AS $key)
			{
				if(strncmp($object_name . '/', $key, strlen($object_name . '/')) == 0)
				{
					$raw = explode('/', $key);
					$total = count($raw);

					$iter = $value;
					for($i = 1; $i < $total-1; $i++)
					{
						$iter = $iter->{$raw[$i]};
					}
					$v = $request->request->get($key);
					$iter->{$raw[$i]} = $v;
				}
			}
		}

		return $value;
	}

	protected function parseAnnotations($annotations)
	{
		if(empty($annotations))
		{
			return array();
		}

		/**
		 * Retrieve the description
		 */
		$description = array();
		preg_match_all('#@description (.*?)\n#s', $annotations, $description);
		$method_description = empty($description[1][0]) ? '' : $description[1][0];

		/**
		 * Params.
		 */
		$params_annotations = array();
		preg_match_all('#@param (([a-zA-Z0-9_]+?) (.*?))\n#s', $annotations, $params_annotations);
		$param_description_list = array();
		foreach($params_annotations[0] AS $key => $p)
		{
			$param_txt = $params_annotations[1][$key];
			$param_name = $params_annotations[2][$key];
			$param_description = $params_annotations[3][$key];
			$param_description_list[$param_name] = $param_description;
		}

		/**
		 * Request body
		 */
		$body = array();
		preg_match_all('#@body\((.*)\)\n#', $annotations, $body);
		$requestBody = empty($body[1][0]) ? '' : $body[1][0];

		/**
		 * Output
		 */
		$jsonOutput = array();
		preg_match_all('#@jsonOutput\((.*)\)\n#', $annotations, $jsonOutput);
		$jsonOut = empty($jsonOutput[1][0]) ? '' : $jsonOutput[1][0];

		/**
		 * HTTP Code
		 */
		$http_code_list = array();
		preg_match_all('#@httpCode\((.*)\)\n#', $annotations, $http_code_list);

		$httpCodeList = array();
		if(!empty($http_code_list[1]))
		{
			foreach($http_code_list[1] AS $httpCode)
			{
				$array = (array)json_decode('[' . $httpCode . ']', TRUE);
				$httpCodeList[] = array('code' => $array[0], 'details' => $array[1]);
			}
		}

		return array(	'description' => $method_description,
						'params' => $param_description_list,
						'request' => $requestBody,
						'response' => $jsonOut,
						'http' => $httpCodeList);
	}

	public function getRoutes()
	{
		$data = $this->classname_routes;

		$routes_list = array();
		foreach($data AS $controller_name => $routes)
		{
			foreach($routes AS $method_type => $actions)
			{
				foreach($actions AS $method_name => $action)
				{
					$routes_list[] = array('path' => $action['path'], 'function' => $action['action'], 'action' => $method_name, 'controller' => strtolower($controller_name), 'method' => $method_type, 'annotations' => $action['annotations']);
				}
			}
		}

		return $routes_list;
	}
}

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
					include_once($dir . '/' . $file);
					$class_name = $controller_name . 'Controller';
					$this->classname_routes[$controller_name] = $this->getRoutesFromClassName($class_name);
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
			foreach($actions AS $type => $action)
			{
				foreach($action AS $name => $meta)
				{
					$route_path = '/';
					if(!($controller_name == 'index' && $name == 'index'))
					{
						// Controller part
						$route_path .= $controller_name . '/';

						// Object id part
						if(!empty($meta['url_parameters']))
						{
							$p = array_shift($meta['url_parameters']);
							$route_path .= '{' . $p . '}/';
						}

						// Action part
						$route_path .= strtolower($name);

						// Page and others part
						foreach($meta['url_parameters'] AS $p)
						{
							$route_path .= '/{' . $p . '}';
						}
					}

					$method = function(Request $request, Response $response, array $args) use($type, $controller_name, $name, $meta) {
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
						return $ctrl->process($type, $controller_name, $name, $meta['action'], $method_params);
					};

					$this->addRoute($type, $route_path, $method);
					$this->routes_list[] = $type . ' ' . $route_path;
					if($name == 'index')
					{
						$route_path = '/' . $controller_name;
						$this->addRoute($type, $route_path, $method);
						$this->routes_list[] = $type . ' ' . $route_path;
					}

					if($controller_name == 'index' && $name == 'index')
					{
						$route_path = '/' . $controller_name . '/' . $name;
						$this->addRoute($type, $route_path, $method);
						$this->routes_list[] = $type . ' ' . $route_path;
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
			$url_param_list = array();
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
				$param_list[$name] = array('type' => $type, 'name' => $name, 'required' => $required);
				if(empty($type))
				{
					$url_param_list[] = $name;
				}
			}

			/**
			 * Save in routes
			 */
			$routes[$method_type][$method_name] = array(
												'controller' => $method->getDeclaringClass()->getName(),
												'action' => $method->getName(),
												'url_parameters' => $url_param_list,
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
						$iter = $iter->$raw[$i];
					}
					$v = $request->request->get($key);
					$iter->$raw[$i] = $v;
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
						$iter = $iter->$raw[$i];
					}
					$v = $request->request->get($key);
					$iter->$raw[$i] = $v;
				}
			}
		}

		return $value;
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
					$routes_list[] = array('function' => $action['action'], 'action' => $method_name, 'controller' => strtolower($controller_name), 'method' => $method_type);
				}
			}
		}

		return $routes_list;
	}
}

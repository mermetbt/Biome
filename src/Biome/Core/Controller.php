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

	public function process($type, $controller_name, $action_name, $method_name, $parameters)
	{
		if($type == 'GET')
		{
			$this->view = new View($this->request, $this->response);
			$this->view->load($controller_name, $action_name);
		}

		$method_params = array();
		foreach($parameters AS $param)
		{
			$method_params[] = $this->parameterInjection($param['type'], $param['name'], $param['required']);
		}

		call_user_func_array(array($this, $method_name), $method_params);

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

	protected function parameterInjection($type, $name, $required)
	{
		$value = NULL;
		if(!empty($type))
		{
			switch($type)
			{
				// Default PHP type
				case 'string':
				case 'int':
					break;
				default: // Class injection

					/**
					 * Collection injection
					 */
					if(substr($type, -strlen('Collection')) == 'Collection')
					{
						// Instanciate the collection
						$collection_name = strtolower(substr($type, 0, -strlen('Collection')));
						$value = Collection::get($collection_name);

						// Check if data are sent
						foreach($this->request()->request->keys() AS $key)
						{
							//echo 'Check collection ', $collection_name, ' over ', $key, '<br/>';
							if(strncmp($collection_name . '/', $key, strlen($collection_name . '/')) == 0)
							{
								$raw = explode('/', $key);
								$total = count($raw);

								$iter = $value;
								for($i = 1; $i < $total-1; $i++)
								{
									$iter = $iter->$raw[$i];
								}
								$v = $this->request()->request->get($key);
								$iter->$raw[$i] = $v;
							}
						}
					}
			}
		}

		return $value;
	}
}

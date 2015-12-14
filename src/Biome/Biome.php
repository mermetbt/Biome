<?php

namespace Biome;

use Biome\Core\Autoload;
use Biome\Core\URL;
use Biome\Core\Route;
use Biome\Core\Error;
use Biome\Core\Rights\AccessRights;
use Biome\Core\HTTP\Request;
use Biome\Core\HTTP\Response;

use Biome\Core\Logger\Logger;

use Symfony\Component\Console\Application;

class Biome
{
	protected static $directories	= NULL;
	protected static $_services		= array();
	protected static $_end_actions	= array();

	public static function start()
	{
		session_name(md5($_SERVER['PHP_SELF']));
		session_start();

		/* Initializing the Framework. */
		Error::init();

		self::declareServices();

		/* Starting. */
		$request = Biome::getService('request');

		Logger::info('Request URI: ' . $request->getUri());

		$dispatcher = Biome::getService('dispatcher');
		$response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

		/* Send the response. */
		$response->send();

		/* Commit. */
		foreach(self::$_end_actions AS $action)
		{
			$action();
		}
	}

	public static function tests()
	{
		self::declareServices();


	}

	protected static function declareServices()
	{
		/* Registering default services. */

		/**
		 * Autoload
		 */
		Autoload::register();

		/**
		 * Biome default logger service.
		 */
		if(!Biome::hasService('logger'))
		{
			Biome::registerService('logger', function() {
				return new \Psr\Log\NullLogger();
			});
		}

		/**
		 * Biome default request.
		 */
		if(!Biome::hasService('request'))
		{
			Biome::registerService('request', function() {
				return Request::createFromGlobals();
			});
		}

		/**
		 * Biome default view service.
		 */
		if(!Biome::hasService('view'))
		{
			Biome::registerService('view', function() {
				return new \Biome\Core\View();
			});
		}

		/**
		 * Biome default rights service.
		 */
		if(!Biome::hasService('rights'))
		{
			Biome::registerService('rights', function() {
				$auth = \Biome\Core\Collection::get('auth');

				if($auth->isAuthenticated())
				{
					$roles = $auth->user->roles;
					foreach($roles AS $role)
					{
						/* If Admin. */
						if($role->role_id == 1)
						{
							return new \Biome\Core\Rights\FreeRights();
						}
						$rights = AccessRights::loadFromJSON($role->role_rights);
					}
					return $rights;
				}

				$rights = AccessRights::loadFromArray(array());

				$rights	->setAttribute('User', 'firstname', TRUE, TRUE)
						->setAttribute('User', 'lastname', TRUE, TRUE)
						->setAttribute('User', 'mail', TRUE, TRUE)
						->setAttribute('User', 'password', TRUE, TRUE)
						->setRoute('GET', 'index', 'index')
						->setRoute('GET', 'auth', 'login')
						->setRoute('POST', 'auth', 'signin')
						->setRoute('POST', 'auth', 'signup');

				return $rights;
			});
		}

		/**
		 * Biome default route service.
		 */
		if(!Biome::hasService('router'))
		{
			Biome::registerService('router', function() {
				$router = new Route();
				$router->autoroute();
				return $router;
			});
		}

		/**
		 * Biome default dispatch service.
		 */
		if(!Biome::hasService('dispatcher'))
		{
			Biome::registerService('dispatcher', function() {
				return Biome::getService('router')->getDispatcher();
			});
		}

		Logger::info('Services registered!');
	}

	public static function shell()
	{
		self::declareServices();
		$app = new Application('Biome Shell', 'development');
		$app->setAutoExit(FALSE);

		/* Initializing the Framework. */
		//Error::init();

		$dirs = self::getDirs('commands');
		foreach($dirs AS $dir)
		{
			$files = scandir($dir);
			foreach($files AS $file)
			{
				if($file[0] == '.')
				{
					continue;
				}

				$command = substr($file, 0, -4);

				if(class_exists($command))
				{
					$command::registerCommands($app);
				}
			}
		}

		$app->run();

		/* Commit. */
		foreach(self::$_end_actions AS $action)
		{
			$action();
		}
	}

	private static function initDirs()
	{
		if(self::$directories !== NULL)
		{
			return TRUE;
		}

		self::$directories = array();

		$dirs = array(
			'controllers'	=> __DIR__ . '/../app/controllers/',
			'models'		=> __DIR__ . '/../app/models/',
			'views'			=> __DIR__ . '/../app/views/',
			'components'	=> __DIR__ . '/../app/components/',
			'collections'	=> __DIR__ . '/../app/collections/',
			'commands'		=> __DIR__ . '/../app/commands/',
		);
		self::registerDirs($dirs);
		return TRUE;
	}

	public static function registerDirs(array $dirs)
	{
		self::initDirs();
		foreach($dirs AS $type => $dir)
		{
			self::$directories[$type][$dir] = $dir;
		}
		return TRUE;
	}

	public static function getDirs($type)
	{
		return self::$directories[$type];
	}

	public static function registerAlias(array $alias)
	{
		/**
		 * TODO: Replace this ugly and unsecure things by a better autoloading.
		 */
		foreach($alias AS $a => $c)
		{
			eval('class ' . $a . ' extends ' . $c . ' {};');
		}
	}

	public static function registerService($service_name, $callable)
	{
		if(!is_callable($callable))
		{
			throw new \Exception('Unable to register a service on a non callable!');
		}
		self::$_services[$service_name]['function'] = $callable;
		return TRUE;
	}

	public static function hasService($service_name)
	{
		return isset(self::$_services[$service_name]);
	}

	public static function getService($service_name)
	{
		if(!isset(self::$_services[$service_name]))
		{
			throw new \Exception('Service undefined ' . $service_name);
		}

		if(!isset(self::$_services[$service_name]['instance']))
		{
			$func = self::$_services[$service_name]['function'];
			self::$_services[$service_name]['instance'] = $func();
		}

		return self::$_services[$service_name]['instance'];
	}

	public static function setFinal($action)
	{
		if(!is_callable($action))
		{
			throw new \Exception('Unable to set final action on a non callable!');
		}
		self::$_end_actions[] = $action;
		return TRUE;
	}
}

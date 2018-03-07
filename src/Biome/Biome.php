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
		/* Initializing the Framework. */
		self::declareServices();

		Error::init();

		/* Starting. */
		$request = Biome::getService('request');

		Logger::info('Request URI: ' . $request->getUri());

		$dispatcher = Biome::getService('dispatcher');
		$response = $dispatcher->dispatch($request->getMethod(), $request->getCanonicPathInfo());

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
		 * Biome default request.
		 */
		if(!Biome::hasService('session'))
		{
			Biome::registerService('session', function() {
				$session = new \Biome\Core\Session\Session();
				$session->start();
				return $session;
			});
		}

		/**
		 * Biome default lang service.
		 */
		if(!Biome::hasService('lang'))
		{
			Biome::registerService('lang', function() {
				$languages = Biome::getService('request')->getLanguages();

				$lang = new \Biome\Core\Lang\XMLLang($languages);

				return $lang;
			});
		}

		/**
		 * Biome default view service.
		 */
		if(!Biome::hasService('view'))
		{
			Biome::registerService('view', function() {
				$view = new \Biome\Core\View();
				$app_name = Biome::getService('lang')->get('app_name');
				$view->setTitle($app_name);
				return $view;
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
					if(\Biome\Core\Auth::isAdmin())
					{
						return new \Biome\Core\Rights\FreeRights();
					}

					$roles = $auth->user->roles;
					$rights_array = array();
					foreach($roles AS $role)
					{
						$rights = (array)json_decode($role->role_rights, TRUE);
						$rights_array = array_merge($rights_array, $rights);
					}

					return AccessRights::loadFromArray($rights_array);
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

	private static function initDirs($use_default_app = TRUE)
	{
		if(self::$directories !== NULL)
		{
			return TRUE;
		}

		self::$directories = array();

		if($use_default_app)
		{
			$dirs = array(
				'controllers'	=> __DIR__ . '/../app/controllers/',
				'models'		=> __DIR__ . '/../app/models/',
				'views'			=> __DIR__ . '/../app/views/',
				'components'	=> __DIR__ . '/../app/components/',
				'collections'	=> __DIR__ . '/../app/collections/',
				'commands'		=> __DIR__ . '/../app/commands/',
				'resources'		=> __DIR__ . '/../resources/',
			);
			self::registerDirs($dirs);
		}
		return TRUE;
	}

	public static function registerDirs(array $dirs, $use_default_app = TRUE)
	{
		self::initDirs($use_default_app);
		foreach($dirs AS $type => $dir)
		{
			if(is_array($dir))
			{
				foreach($dir AS $d)
				{
					self::$directories[$type][$d] = $d;
				}
				continue;
			}
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

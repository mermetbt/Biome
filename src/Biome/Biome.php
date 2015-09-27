<?php

namespace Biome;

use Biome\Core\URL;
use Biome\Core\Route;
use Biome\Core\Error;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Biome
{
	protected static $directories	= array();
	protected static $_services		= array();

	public static function start()
	{
		session_start();

		/* Initializing the Framework. */
		Error::init();

		$request = URL::getRequest();

		/* Routing. */
		$router = new Route($request, array(__DIR__ . '/../app/controllers/', self::getDir('controllers')));
		$router->autoroute();

		/* Dispatch. */
		$dispatcher = $router->getDispatcher();
		$response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

		/* Send the response. */
		$response->send();

		/* Commit. */
	}

	public static function registerDirs(array $dirs)
	{
		self::$directories = $dirs;
	}

	public static function getDir($type)
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
		self::$_services[$service_name]['function'] = $callable;
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
}

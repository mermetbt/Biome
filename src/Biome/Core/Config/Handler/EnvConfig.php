<?php

namespace Biome\Core\Config\Handler;

use Biome\Core\Config\ConfigInterface;
use Biome\Core\Logger\Logger;

class EnvConfig implements ConfigInterface
{
	protected $getenv_func = NULL;

	public function __construct($config_file = NULL)
	{
		if($config_file == NULL)
		{
			$config_file = APP_DIR . '/.env';
		}

		$this->getenv_func = 'getenv';
		if(function_exists('apache_getenv'))
		{
			$this->getenv_func = 'apache_getenv';
		}

		if(!file_exists($config_file))
		{
			Logger::warning('Unable to find the configuration file (only the global environment variable will be available)!');
			return;
		}

		$config = file_get_contents($config_file);

		$lines = explode(PHP_EOL, $config);
		foreach($lines AS $env)
		{
			$env = trim($env);
			if(!empty($env))
			{
				if(function_exists('apache_setenv'))
				{
					$raw = explode('=', $env);
					if(count($raw) == 2)
					{
						apache_setenv($raw[0], $raw[1]);
					}
					else
					{
						Logger::warning('Invalid environment definition: ' . $env);
					}
					continue;
				}

				putenv($env);
			}
		}
		unset($lines);
		unset($config);
	}

	public function get($attribute, $default_value = NULL)
	{
		$func = $this->getenv_func;
		$value = $func($attribute);
		if($value === FALSE)
		{
			return $default_value;
		}
		return $value;
	}
}

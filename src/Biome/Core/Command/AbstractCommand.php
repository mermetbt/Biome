<?php

namespace Biome\Core\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand
{
	protected $output;

	protected function __construct(OutputInterface $output)
	{
		$this->output = $output;
	}

	public static function registerCommands(Application $console)
	{
		$class = get_called_class();
		$reflection = new \ReflectionClass($class);

		foreach($reflection->getMethods() AS $method)
		{
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

			$group_name = strtolower(substr($class, 0, -strlen('Command')));
			$command_name = $method->getName();

			/**
			 * Read comments.
			 */
			$comment = $method->getDocComment();

			$method_description = '';
			$param_description_list = array();
			if(!empty($comment))
			{
				/* Method description. */
				preg_match_all('#@description (.*?)\n#s', $comment, $description);
				$method_description = empty($description[1][0]) ? '' : $description[1][0];

				/* Params. */
				preg_match_all('#@param (([a-zA-Z0-9]+?) (.*?))\n#s', $comment, $params_annotations);
				foreach($params_annotations[0] AS $key => $p)
				{
					$param_txt = $params_annotations[1][$key];
					$param_name = $params_annotations[2][$key];
					$param_description = $params_annotations[3][$key];
					$param_description_list[$param_name] = $param_description;
				}
			}

			/**
			 * Parameters handling.
			 */
			$parameters = array();
			$params_list = array();
			foreach($method->getParameters() AS $param)
			{
				$name = $param->getName();

				// PHP 7
				if(method_exists($param, 'getType'))
				{
					$type = $param->getType();
					$required = !$param->allowsNull();
				}
				// PHP 5
				else
				{
					$matches = array();
					preg_match('/\[(.*)\]/', (string)$param, $matches);

					$raw = explode(' ', trim($matches[1]));

					$required = ($raw[0] == '<required>') ? TRUE : FALSE;
					$type = ($raw[1][0] == '$') ? '' : $raw[1];
				}

				$required_constant = $required ? InputArgument::REQUIRED : NULL;
				$param_description = !empty($param_description_list[$name]) ? $param_description_list[$name] : '';
				$parameters[] = new InputArgument($name, $required_constant, $param_description);
				$params_list[] = $name;
			}

			$console->register($group_name . ':' . $command_name)
					->setDefinition($parameters)
					->setDescription($method_description)
					->setCode(function (InputInterface $input, OutputInterface $output) use($class, $command_name, $params_list) {

						$parameters = array();
						foreach($params_list AS $param_name)
						{
							$parameters[] = $input->getArgument($param_name);
						}

						$object = new $class($output);
						$result = call_user_func_array(array($object, $command_name), $parameters);
						return $result;
					});
		}
	}
}

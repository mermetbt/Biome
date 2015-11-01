<?php

namespace Biome\Core\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
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

			$group_name = strtolower(substr($class, 0, -strlen('Command')));
			$command_name = $method->getName();

			/**
			 * Parameters handling.
			 */
			$parameters = array();
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
					preg_match('/\[(.*)\]/', $param_str, $matches);

					$raw = explode(' ', trim($matches[1]));

					$required = ($raw[0] == '<required>') ? TRUE : FALSE;
					$type = ($raw[1][0] == '$') ? '' : $raw[1];
				}

				$required_constant = $required ? InputArgument::REQUIRED : '';
				$parameters[] = new InputArgument($name, $required_constant, 'Directory name');
			}

			$console->register($group_name . ':' . $command_name)
					->setDefinition($parameters)
					->setDescription('Displays the files in the given directory')
					->setCode(function (InputInterface $input, OutputInterface $output) {
						$dir = $input->getArgument('dir');

						$output->writeln(sprintf('Dir listing for <info>%s</info>', $dir));
					});

		}
	}
}

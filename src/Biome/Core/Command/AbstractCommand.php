<?php

namespace Biome\Core\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application;

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

			$console->register($group_name . ':' . $command_name)
					->setDefinition(array(
						new InputArgument('dir', InputArgument::REQUIRED, 'Directory name'),
					))
					->setDescription('Displays the files in the given directory')
					->setCode(function (InputInterface $input, OutputInterface $output) {
						$dir = $input->getArgument('dir');

						$output->writeln(sprintf('Dir listing for <info>%s</info>', $dir));
					});

		}
	}
}

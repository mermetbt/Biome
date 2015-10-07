<?php

namespace Biome\Core\Command;

abstract class AbstractCommand
{
	public static function listCommands()
	{
		$class = get_called_class();
		$reflection = new \ReflectionClass($class);

		foreach($reflection->getMethods() AS $method)
		{
			if($method->isStatic())
			{
				continue;
			}
			echo '- ', strtolower(substr($class, 0, -strlen('Command')) . ':' . $method->getName()), PHP_EOL;
		}
	}
}

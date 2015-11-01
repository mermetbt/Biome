<?php

namespace Biome\Core\Logger\Handler;

use Psr\Log\AbstractLogger;

class ConsoleLogger extends AbstractLogger
{
	public function log($level, $message, array $context = array())
	{
		echo date('Y-m-d H:i:s'), ' [', $level, '] ', $message, ' ', PHP_EOL;
		if(!empty($context))
		{
			print_r($context);
		}
	}
}

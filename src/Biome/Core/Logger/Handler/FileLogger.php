<?php

namespace Biome\Core\Logger\Handler;

use Psr\Log\AbstractLogger;

class FileLogger extends AbstractLogger
{
	protected $fp;

	protected $_request_id;

	public function __construct($filename)
	{
		if(file_exists($filename) && 0666 !== (fileperms($filename) & 0777))
		{
			@chmod($filename, 0666); // A+RW
		}

		$this->fp = fopen($filename, 'a+');
		if(!$this->fp)
		{
			throw new \Exception('Unable to write in filename ' . $filename . '!');
		}

		$this->_request_id = uniqid();
	}

	public function __destruct()
	{
		fclose($this->fp);
	}

	public function log($level, $message, array $context = array())
	{
		$m = date('Y-m-d H:i:s') . ' ' . $this->_request_id . ' [' . $level . '] ' . $message . PHP_EOL;
		if(!empty($context))
		{
			$m .= print_r($context, true) . PHP_EOL;
		}
		fwrite($this->fp, $m, strlen($m));
	}
}

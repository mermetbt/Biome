<?php

namespace Biome\Core\Cache;

class StaticCache implements CacheInterface
{
	protected $cacheDir = '';

	public function __construct($cacheDir)
	{
		if(!file_exists($cacheDir))
		{
			mkdir($cacheDir);
		}
		$this->cacheDir = $cacheDir;
	}

	protected function serialize($value)
	{
		return serialize($value);
	}

	protected function unserialize($value)
	{
		return unserialize($value);
	}

	public function store($name, $value, $expiration = NULL)
	{
		$filename = $this->cacheDir . '/' . md5($name) . '.php';
		$fp = fopen($filename, 'w');
		if(!$fp)
		{
			throw new \Exception('Unable to open file: ' . $filename);
		}

		fwrite($fp, $this->serialize($value));
		fclose($fp);
		chmod($filename, 0777);
		return TRUE;
	}

	public function get($name)
	{
		$filename = $this->cacheDir . '/' . md5($name) . '.php';
		if(!file_exists($filename))
		{
			return NULL;
		}

		if(($size = filesize($filename)) == 0)
		{
			return NULL;
		}

		$fp = fopen($filename, 'r');
		$value = fread($fp, $size);
		fclose($fp);
		return $this->unserialize($value);
	}

	public function clear($name)
	{
		$filename = $this->cacheDir . '/' . md5($name) . '.php';
		if(!file_exists($filename))
		{
			return FALSE;
		}
		unlink($filename);
		return TRUE;
	}

	public function flush()
	{
		$dir = scandir($this->cacheDir);
		foreach($dir AS $file)
		{
			if($file[0] == '.')
			{
				continue;
			}
			unlink($file);
		}
	}
}

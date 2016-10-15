<?php

namespace Biome\Core\Filesystem;

class Directory
{
	protected $_dirname = NULL;

	public function __construct($dirname)
	{
		$this->_dirname = $dirname;
	}

	public function setReadable($readable)
	{
		// TODO
		return TRUE;
	}

	public function setWritable($writable)
	{
		// TODO
		return TRUE;
	}

	public function isReadable()
	{
		if(is_dir($this->_dirname))
		{
			return is_readable($this->_dirname);
		}

		$parent_dir = $this->_dirname;
		do
		{
			$parent_dir = dirname($parent_dir);
		}
		while(!file_exists($parent_dir));
		return is_readable($parent_dir);
	}

	public function isWritable()
	{
		if(is_dir($this->_dirname))
		{
			return is_writable($this->_dirname);
		}

		$parent_dir = $this->_dirname;
		do
		{
			$parent_dir = dirname($parent_dir);
		}
		while(!file_exists($parent_dir));
		return is_writable($parent_dir);
	}

	public function create($true_if_exists = TRUE)
	{
		if(file_exists($this->_dirname))
		{
			return $true_if_exists;
		}

		if(!$this->isWritable())
		{
			return FALSE;
		}

		mkdir($this->_dirname, 0777, TRUE);
		return TRUE;
	}

	public function rename($name)
	{
		// TODO
	}

	public function delete()
	{
		// TODO
	}

	public function copy($new_dir)
	{
		// TODO
	}
}

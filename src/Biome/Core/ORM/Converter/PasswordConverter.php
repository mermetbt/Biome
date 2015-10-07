<?php

namespace Biome\Core\ORM\Converter;

class PasswordConverter implements ConverterInterface
{
	public function get($value)
	{
		return '';
	}

	public function set($value)
	{
		return md5($value);
	}
}

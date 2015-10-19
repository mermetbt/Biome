<?php

namespace Biome\Core\ORM\Converter;

class DateTimeConverter implements ConverterInterface
{
	public function get($value)
	{
		return new DateTime($value);
	}

	public function set($value)
	{
		if($value instanceof DateTime)
		{
			return $value->format('Y-m-d H:i:s');
		}
		else
		{
			return $value;
		}
	}
}

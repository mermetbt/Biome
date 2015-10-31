<?php

namespace Biome\Core\ORM\Converter;

use Biome\Core\Utils\DateTime;

class DateTimeConverter implements ConverterInterface
{
	public function get($value)
	{
		if($value instanceof DateTime)
		{
			return $value;
		}

		if(!is_string($value))
		{
			throw new \Exception('Wrong date value given! ' . print_r($value, TRUE));
		}

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

<?php

namespace Biome\Core\ORM\Converter;

interface ConverterInterface
{
	public function set($value);

	public function get($value);
}

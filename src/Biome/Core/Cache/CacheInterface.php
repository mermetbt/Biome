<?php

namespace Biome\Core\Cache;

interface CacheInterface
{
	public function store($name, $value, $expiration = NULL);

	public function get($name);

	public function clear($name);

	public function flush();
}

<?php

namespace Biome\Core\ORM;

class LazyFetcher
{
	public function __construct($query_set, $field_name)
	{
		$this->query_set = $query_set;
		$this->field_name = $field_name;
	}

	public function fetch()
	{
		$result = array();

		foreach($this->query_set AS $parent_object)
		{
			$id = $parent_object->getId($this->field_name);
			$result[] = $id;
		}

		return $result;
	}
}

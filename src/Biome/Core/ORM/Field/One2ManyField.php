<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;
use Biome\Core\ORM\ObjectLoader;
use Biome\Core\ORM\QuerySetFieldInterface;
use Biome\Core\ORM\QuerySet;

class One2ManyField extends AbstractField implements QuerySetFieldInterface
{
	protected $object_name	= NULL;
	protected $foreign_key	= NULL;

	public function __construct($object_name, $foreign_key = NULL)
	{
		$this->object_name	= $object_name;
		$this->foreign_key	= $foreign_key;
	}

	public function generateQuerySet(QuerySet $query_set, $field_name)
	{
		$object_name	= $this->object_name;

		// Load objects
		$object			= ObjectLoader::get($object_name);

		// Handle One2Many
		return $object::all()->associate($this->foreign_key, $query_set, $field_name);
	}

	public function getDefaultValue()
	{
		return array();
	}
}

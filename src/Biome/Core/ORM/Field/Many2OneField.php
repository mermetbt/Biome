<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;
use Biome\Core\ORM\ObjectLoader;
use Biome\Core\ORM\QuerySetFieldInterface;
use Biome\Core\ORM\QuerySet;

class Many2OneField extends AbstractField implements QuerySetFieldInterface
{
	protected $object		= NULL;
	protected $object_name	= NULL;
	protected $foreign_key	= NULL;

	public function __construct($object_name, $foreign_key = NULL)
	{
		$this->object		= ObjectLoader::get($object_name);
		$this->object_name	= $object_name;
		$this->foreign_key	= !empty($foreign_key) ? $foreign_key : $this->object->parameters()['primary_key'];
	}

	public function generateQuerySet(QuerySet $query_set, $field_name)
	{
		// Handle Many2One
		$m2o_object_name	= $this->object_name;
		ObjectLoader::load($m2o_object_name);
		return $m2o_object_name::all()->associate($this->foreign_key, $query_set, $field_name);
	}

	public function getObject($primary_key_value)
	{
		$m2o_object_name	= $this->object_name;
		$object = ObjectLoader::get($m2o_object_name);
		$object->sync($primary_key_value);
		return $object;
	}
}

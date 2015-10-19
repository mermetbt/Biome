<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;
use Biome\Core\ORM\ObjectLoader;
use Biome\Core\ORM\QuerySetFieldInterface;
use Biome\Core\ORM\QuerySet;

class Many2ManyField extends AbstractField implements QuerySetFieldInterface
{
	protected $object_name	= NULL;
	protected $foreign_key	= NULL;

	protected $link_object_name	= NULL;
	protected $link_foreign_key	= NULL;

	public function __construct($object_name, $foreign_key = NULL, $link_object_name = NULL, $link_foreign_key = NULL)
	{
		$this->object_name	= $object_name;
		$this->foreign_key	= $foreign_key;

		if(!empty($link_object_name))
		{
			$this->link_object_name = $link_object_name;
		}

		if(!empty($link_foreign_key))
		{
			$this->link_foreign_key = $link_foreign_key;
		}
	}

	public function generateQuerySet(QuerySet $query_set, $field_name)
	{
		$lnk_object_name	= $this->link_object_name;
		$m2m_object_name	= $this->object_name;

		// Load objects
		$lnk_object			= ObjectLoader::get($lnk_object_name);
		$m2m_object			= ObjectLoader::get($m2m_object_name);

		// Handle One2Many
		$lnk_attribute_name	= $this->link_foreign_key;
		$lnk_qs				= $lnk_object_name::all()->associate($lnk_attribute_name, $query_set, $field_name);

		// Handle Many2Many
		$m2m_attribute_name	= $this->foreign_key;
		return $m2m_object_name::all()->associate($m2m_attribute_name, $lnk_qs, $lnk_attribute_name);
	}

	public function getDefaultValue()
	{
		return array();
	}
}

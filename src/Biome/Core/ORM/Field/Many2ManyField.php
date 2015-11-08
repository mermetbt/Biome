<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;
use Biome\Core\ORM\ObjectLoader;
use Biome\Core\ORM\QuerySetFieldInterface;
use Biome\Core\ORM\QuerySet;

class Many2ManyField extends AbstractField implements QuerySetFieldInterface
{
	protected $editable = FALSE;

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

	public function getLinkObject()
	{
		return $this->object = ObjectLoader::get($this->link_object_name);
	}

	public function getLinkForeignKey()
	{
		return $this->link_foreign_key;
	}

	public function getDestinationObject()
	{
		return $this->object = ObjectLoader::get($this->object_name);
	}

	public function getDestinationForeignKey()
	{
		return $this->foreign_key;
	}

	public function generateQuerySet(QuerySet $query_set, $field_name)
	{
		$lnk_object_name	= $this->link_object_name;
		$m2m_object_name	= $this->object_name;

		// Load objects
		$lnk_object			= ObjectLoader::load($lnk_object_name);
		$m2m_object			= ObjectLoader::load($m2m_object_name);

		// Handle One2Many
		$lnk_attribute_name	= $this->link_foreign_key;
		$lnk_qs				= $lnk_object_name::all()->associate($lnk_attribute_name, $query_set, $field_name);

		// Handle Many2Many
		$m2m_attribute_name	= $this->foreign_key;

		return $m2m_object_name::all()->associate($m2m_attribute_name, $lnk_qs, $m2m_attribute_name);
	}

	public function operateChanges($object, QuerySet $query_set)
	{
		$object_id = $object->getId();

		$lnk_object_name = $this->link_object_name;
		$lnk_field = $this->link_foreign_key;
		$m2m_field = $this->foreign_key;

		$to_delete = array();
		foreach($query_set->modifiers() AS $id => $m)
		{
			if($m == 'add')
			{
				$l = new $lnk_object_name();
				$l->$lnk_field = $object_id;
				$l->$m2m_field = $id;
				$l->save();
			}
			else
			if($m == 'remove')
			{
				$to_delete[] = $id;
			}
			else
			{
				throw new \Exception('Unrecognized modifier!');
			}
		}

		if(empty($to_delete))
		{
			return TRUE;
		}

		$delete = $lnk_object_name::all()->filter(array(
			array($lnk_field, '=', $object_id),
			array($m2m_field, 'in', $to_delete)
		));

		foreach($delete AS $d)
		{
			$d->delete();
		}

		return TRUE;
	}

	public function getDefaultValue()
	{
		$m2m_object_name = $this->object_name;
		return $m2m_object_name::all();
	}
}

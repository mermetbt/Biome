<?php

namespace Biome\Core\ORM\Field;

use Biome\Core\ORM\AbstractField;
use Biome\Core\ORM\ObjectLoader;

class Many2OneField extends AbstractField
{
	protected $object		= NULL;
	protected $object_name	= NULL;
	protected $foreign_key	= NULL;

	public function __construct($object_name, $foreign_key = NULL)
	{
		$this->object		= ObjectLoader::load($object_name);
		$this->object_name	= $object_name;
		$this->foreign_key	= !empty($foreign_key) ? $foreign_key : $this->object->parameters()['primary_key'];
	}
}

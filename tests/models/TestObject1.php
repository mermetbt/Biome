<?php

use Biome\Core\ORM\Models;

use Biome\Core\ORM\Field\PrimaryField;
use Biome\Core\ORM\Field\TextField;
use Biome\Core\ORM\Field\DateField;
use Biome\Core\ORM\Field\TimeField;
use Biome\Core\ORM\Field\EnumField;
use Biome\Core\ORM\Field\DoubleField;
use Biome\Core\ORM\Field\Many2OneField;

class TestObject1 extends Models
{
	public function parameters()
	{
		return array(
					'table'			=> 'objects_1',
					'primary_key'	=> 'object_id',
					'reference'		=> 'object_name'
		);
	}

	public function fields()
	{
		$this->object_id		= PrimaryField::create()
								->setLabel('@string/object_id');

		$this->object_name	= TextField::create(32)
								->setLabel('@string/object_name')
								->setRequired(TRUE);

		$this->user_id		= Many2OneField::create('User')
								->setLabel('@string/user_id');

		$this->date		= DateField::create();
		$this->time 	= TimeField::create();

		$this->value 	= DoubleField::create()
							->setDefaultValue(5.0)
							->setRequired(TRUE);

		$this->enumerate = EnumField::create(array(
								'first' => 'First',
								'second' => 'Second')
							)
						  ->setDefaultValue('first');
	}
}

<?php

use Biome\Core\ORM\Models;

use Biome\Core\ORM\Field\PrimaryField;
use Biome\Core\ORM\Field\TextField;
use Biome\Core\ORM\Field\Many2ManyField;

class Role extends Models
{
	public function parameters()
	{
		return array(
					'table'			=> 'roles',
					'primary_key'	=> 'role_id',
		);
	}

	public function fields()
	{
		$this->role_id		= PrimaryField::create()
								->setLabel('Role ID');

		$this->role_name	= TextField::create(32)
								->setLabel('Role');

		$this->users		= Many2ManyField::create('User', 'user_id', 'UserRole', 'role_id')
								->setLabel('Users');
	}
}
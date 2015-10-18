<?php

use Biome\Core\ORM\Models;

use Biome\Core\ORM\Field\PrimaryField;
use Biome\Core\ORM\Field\Many2OneField;

class UserRole extends Models
{
	public function parameters()
	{
		return array(
					'table'			=> 'users_roles',
					'primary_key'	=> array('user_id', 'role_id'),
		);
	}

	public function fields()
	{
		$this->role_id		= PrimaryField::create()
								->setLabel('Role');

		$this->user_id		= PrimaryField::create()
								->setLabel('User');
	}
}


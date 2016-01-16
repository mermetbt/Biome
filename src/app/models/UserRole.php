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
		$this->role_id		= Many2OneField::create('Role')
								->setLabel('@string/role')
								->setRequired(TRUE);

		$this->user_id		= Many2OneField::create('User')
								->setLabel('@string/user')
								->setRequired(TRUE);
	}
}


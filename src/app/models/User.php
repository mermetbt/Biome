<?php

use Biome\Core\ORM\Models;

use Biome\Core\ORM\Field\PrimaryField;
use Biome\Core\ORM\Field\TextField;
use Biome\Core\ORM\Field\EmailField;
use Biome\Core\ORM\Field\PasswordField;

class User extends Models
{
	public function parameters()
	{
		return array(
					'database'		=> 'biome',
					'table'			=> 'users',
					'primary_key'	=> 'user_id',
		);
	}

	public function fields()
	{
		$this->user_id		= PrimaryField::create()
								->setLabel('User ID');

		$this->firstname	= TextField::create(32)
								->setLabel('Firstname');

		$this->lastname		= TextField::create(32)
								->setLabel('Lastname');

		$this->mail			= EmailField::create()
								->setLabel('E-Mail')
								->setRequired(TRUE);

		$this->password		= PasswordField::create(32)
								->setLabel('Password')
								->setRequired(TRUE);
	}
}

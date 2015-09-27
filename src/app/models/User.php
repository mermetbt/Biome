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
		$this->user_id		= new PrimaryField();
		$this->firstname	= new TextField();
		$this->lastname		= new TextField();
		$this->mail			= new EmailField();
		$this->password		= new PasswordField();
	}
}

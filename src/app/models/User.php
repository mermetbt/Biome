<?php

use Biome\Core\ORM\Models;

use Biome\Core\ORM\Field\TextField;
use Biome\Core\ORM\Field\EmailField;
use Biome\Core\ORM\Field\PasswordField;

class User extends Models
{
	public function fields()
	{
		$this->firstname	= new TextField();
		$this->lastname		= new TextField();
		$this->mail			= new EmailField();
		$this->password		= new PasswordField();
	}
}

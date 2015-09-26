<?php

use Biome\Core\ORM\Models;

use Biome\Core\ORM\Field\TextField;
use Biome\Core\ORM\Field\PasswordField;

class User extends Models
{
	public function fields()
	{
		$this->username = new TextField();
		$this->password = new PasswordField();
	}
}


<?php

use Biome\Core\ORM\Models;

use Biome\Core\ORM\Field\PrimaryField;
use Biome\Core\ORM\Field\TextField;
use Biome\Core\ORM\Field\BooleanField;
use Biome\Core\ORM\Field\EmailField;
use Biome\Core\ORM\Field\PasswordField;
use Biome\Core\ORM\Field\DateTimeField;
use Biome\Core\ORM\Field\Many2ManyField;

use Biome\Core\ORM\RawSQL;

use Biome\Core\ORM\Converter\PasswordConverter;

class User extends Models
{
	public function parameters()
	{
		return array(
					'table'			=> 'users',
					'primary_key'	=> 'user_id',
					'search'		=> array('firstname', 'lastname', 'mail')
		);
	}

	public function fields()
	{
		$this->user_id		= PrimaryField::create()
								->setLabel('@string/user_id');

		$this->firstname	= TextField::create(32)
								->setLabel('@string/firstname')
								->setRequired(TRUE);

		$this->lastname		= TextField::create(32)
								->setLabel('@string/lastname')
								->setRequired(TRUE);

		$this->mail			= EmailField::create()
								->setLabel('@string/mail')
								->setRequired(TRUE);

		$this->password		= PasswordField::create(32)
								->setLabel('@string/password')
								->setRequired(TRUE)
								->setConverter(new PasswordConverter());

		$this->user_active	= BooleanField::create()
								->setLabel('@string/active')
								->setRequired(TRUE)
								->setDefaultValue(TRUE);

		$this->creation_date	= DateTimeField::create()
									->setLabel('@string/creation_date')
									->setRequired(TRUE)
									->setEditable(FALSE)
									->setDefaultValue(RawSQL::select('CURRENT_TIMESTAMP'));

		$this->roles		= Many2ManyField::create('Role', 'role_id', 'UserRole', 'user_id')
								->setLabel('@string/roles');
	}

	public function __toString()
	{
		return $this->firstname . ' ' . $this->lastname;
	}
}

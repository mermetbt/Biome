<?php

use Biome\Core\Collection\RequestCollection;

class UsersCollection extends RequestCollection
{
	protected $map = array(
		'user' => 'User',
		'users' => array()
	);

	public $password;
	public $password_confirm;

	public function getUsers()
	{
		if(empty($this->users))
		{
			$this->users = User::all();
		}
		return $this->users;
	}
}

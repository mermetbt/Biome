<?php

use Biome\Core\Collection\SessionCollection;

class AuthCollection extends SessionCollection
{
	protected $map = array(
		'user' => 'User',
		'users' => array()
	);

	public function getUsers()
	{
		return $this->users;
	}

	public function storeUser(User $user)
	{
		$list = $this->users;
		$list[] = $user;
		$this->users = $list;
	}
}

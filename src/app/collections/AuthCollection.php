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
		$list[] = clone $user;
		$this->users = $list;
	}

	public function isAuthenticated()
	{
		return $this->user->getId() !== NULL;
	}

	public function logout()
	{
		$this->user->detach();
		return TRUE;
	}
}

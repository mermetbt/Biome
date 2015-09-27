<?php

use Biome\Core\Collection\RequestCollection;

class UsersCollection extends RequestCollection
{
	protected $map = array(
		'users' => array()
	);

	public function getUsers()
	{
		if(empty($this->users))
		{
			$this->users = User::all();
		}
		return $this->users;
	}
}

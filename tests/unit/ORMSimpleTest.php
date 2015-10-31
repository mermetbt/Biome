<?php

namespace Biome\Test;

use \User;

class ORMSimpleTestTest extends \PHPUnit_Framework_TestCase
{
	public function testRetrieveAllUsers()
	{
		$users = User::all();

		$users_list = array();
		foreach($users AS $user)
		{
			$users_list[] = $user;
		}
		$this->assertNotEmpty($users_list);
	}

	public function testCreateAndGetUser()
	{
		/* Create user. */
		$u = new User();

		/* Set and check storage. */
		$u->firstname = 'Jean';
		$this->assertEquals('Jean', $u->firstname);

		$u->lastname = 'Dupont';
		$this->assertEquals('Dupont', $u->lastname);

		$u->mail = 'jean.dupont@test.com';
		$this->assertEquals('jean.dupont@test.com', $u->mail);

		$u->password = 'My Password';
		$this->assertEquals('', $u->password);

		/* Store */
		$this->assertTrue($u->save());
		$this->assertEquals('Jean', $u->firstname);
		$this->assertEquals('Dupont', $u->lastname);
		$this->assertEquals('jean.dupont@test.com', $u->mail);
		$this->assertEquals('', $u->password);

		/* Get the user */
		$user_id = $u->getId();
		$this->assertNotNull($user_id);

		$user = User::get($user_id);
		$this->assertNotNull($user);

		/* Check consistency */
		$this->assertEquals('Jean', $user->firstname);
		$this->assertEquals('Dupont', $user->lastname);
		$this->assertEquals('jean.dupont@test.com', $user->mail);

		/* Update */
		$user->firstname = 'Alphonse';
		$this->assertEquals('Alphonse', $user->firstname);

		$this->assertTrue($user->save());
		$this->assertEquals('Alphonse', $user->firstname);
		$this->assertEquals('Dupont', $user->lastname);
		$this->assertEquals('jean.dupont@test.com', $user->mail);
		$this->assertEquals('', $user->password);

		/* Delete */
		$this->assertTrue($user->delete());
		$user = User::all()->filter(array(array('user_id', '=', $user_id)))->fetch();
		$this->assertEmpty($user);
	}
}

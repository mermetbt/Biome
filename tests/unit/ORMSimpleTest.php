<?php

namespace Biome\Test;

use \User;
use \Role;
use \UserRole;

class ORMSimpleTest extends \PHPUnit_Framework_TestCase
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

	public function testRoleAssociationWithLinkObject()
	{
		/* Create user. */
		$user = new User();

		$user->firstname = 'Jean';
		$this->assertEquals('Jean', $user->firstname);

		$user->lastname = 'Dupont';
		$this->assertEquals('Dupont', $user->lastname);

		$user->mail = 'jean.dupont@test.com';
		$this->assertEquals('jean.dupont@test.com', $user->mail);

		$user->password = 'My Password';
		$this->assertEquals('', $user->password);

		$this->assertTrue($user->save());
		$this->assertEquals('Jean', $user->firstname);
		$this->assertEquals('Dupont', $user->lastname);
		$this->assertEquals('jean.dupont@test.com', $user->mail);
		$this->assertEquals('', $user->password);

		/* Create role. */
		$role = new Role();
		$role->role_name = 'TEST ROLE';
		$this->assertEquals('TEST ROLE', $role->role_name);
		$this->assertTrue($role->save());
		$this->assertEquals('TEST ROLE', $role->role_name);

		/* Create association. */
		$ur = new UserRole();

		$ur->role = $role; // One way
		$this->assertEquals($role->getId(), $ur->role_id);

		$ur->user_id = $user->getId(); // Another way
		$this->assertEquals($user->getId(), $ur->user_id);

		$this->assertTrue($ur->save());

		$this->assertEquals($role->getId(), $ur->role_id);
		$this->assertEquals($user->getId(), $ur->user_id);

		/* Delete */
		$ur->delete();
		$user->delete();
		$role->delete();
	}

	public function testRoleAssociationWithAutoSave()
	{
		/* Create user. */
		$user = new User();

		$user->firstname = 'Jean';
		$this->assertEquals('Jean', $user->firstname);

		$user->lastname = 'Dupont';
		$this->assertEquals('Dupont', $user->lastname);

		$user->mail = 'jean.dupont@test.com';
		$this->assertEquals('jean.dupont@test.com', $user->mail);

		$user->password = 'My Password';
		$this->assertEquals('', $user->password);

		/* Create role. */
		$role = new Role();
		$role->role_name = 'TEST ROLE';
		$this->assertEquals('TEST ROLE', $role->role_name);

		/* Create association. */
		$ur = new UserRole();

		$ur->role = $role;
		$ur->user = $user;

		$this->assertTrue($ur->save());

		$this->assertEquals($role->getId(), $ur->role_id);
		$this->assertEquals($user->getId(), $ur->user_id);

		/* Delete */
		$ur->delete();
		$user->delete();
		$role->delete();
	}

	public function testRoleAssociationWithQuerySet()
	{
		/* Create role. */
		$role = new Role();
		$role->role_name = 'TEST ROLE';
		$this->assertEquals('TEST ROLE', $role->role_name);

		/* Create user. */
		$user = new User();

		$user->firstname = 'Jean';
		$this->assertEquals('Jean', $user->firstname);

		$user->lastname = 'Dupont';
		$this->assertEquals('Dupont', $user->lastname);

		$user->mail = 'jean.dupont@test.com';
		$this->assertEquals('jean.dupont@test.com', $user->mail);

		$user->password = 'My Password';
		$this->assertEquals('', $user->password);

		$this->assertInstanceOf('\Biome\Core\ORM\QuerySet', $user->roles);

		$this->assertEmpty($user->roles);

		$user->roles[] = $role;

		$this->assertNotEmpty($user->roles);

		$this->assertTrue($user->save());
		$this->assertEquals('Jean', $user->firstname);
		$this->assertEquals('Dupont', $user->lastname);
		$this->assertEquals('jean.dupont@test.com', $user->mail);
		$this->assertEquals('', $user->password);

		/**
		 * From the current object.
		 */
		$this->assertNotEmpty($user->roles);

		$role_found = false;
		$role_count = 0;
		foreach($user->roles AS $r)
		{
			if($r->getId() == $role->getId())
			{
				$role_found = true;
			}
			$role_count++;
		}

		$this->assertTrue($role_found);
		$this->assertEquals(1, $role_count);

		/**
		 * From a new object.
		 */
		$user_id = $user->getId();

		$stored_user = User::get($user_id);

		$this->assertNotEmpty($stored_user->roles);

		$role_found = false;
		$role_count = 0;
		foreach($stored_user->roles AS $r)
		{
			if($r->getId() == $role->getId())
			{
				$role_found = true;
			}
			$role_count++;
		}

		$this->assertTrue($role_found);
		$this->assertEquals(1, $role_count);
	}

	public function testMultipleSave()
	{
		$r = new Role();

		$r->role_name = 'TEST';

		$this->assertTrue($r->save());
		$this->assertTrue($r->save());

		$r->role_name = 'TEST2';
		$this->assertTrue($r->save());
		$this->assertTrue($r->save());

		$r2 = Role::get($r->getId());
		$this->assertTrue($r2->save());
		$this->assertTrue($r2->save());
	}

	public function testForcePrimaryKey()
	{
		$r = new Role();

		$r->role_id = 66677;
		$this->assertEquals(66677, $r->role_id);

		$r->role_name = 'TESTPK';
		$this->assertEquals('TESTPK', $r->role_name);

		$this->assertTrue($r->save());

		$this->assertEquals(66677, $r->role_id);

		$r2 = Role::get(66677);
		$this->assertNotNull($r2);
		$this->assertEquals('TESTPK', $r->role_name);

		$r2->delete();
	}
}

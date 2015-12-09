<?php

namespace Biome\Test;

use \User;
use \Role;
use \UserRole;

class ORMFilterTest extends \PHPUnit_Framework_TestCase
{
	public function testFilterMany2Many()
	{
		/* Create user. */
		$u = new User();

		/* Set and check storage. */
		$u->firstname = 'Jean2';
		$this->assertEquals('Jean2', $u->firstname);

		$u->lastname = 'Dupont2';
		$this->assertEquals('Dupont2', $u->lastname);

		$u->mail = 'jean.dupont2@test.com';
		$this->assertEquals('jean.dupont2@test.com', $u->mail);

		$u->password = 'My Password';
		$this->assertEquals('', $u->password);

		$this->assertTrue($u->save());

		$users = User::all()->filter(
					array('firstname', '=', 'Jean2'),
					array('lastname', '=', 'Dupont2'),
					array('mail', '=', 'jean.dupont2@test.com')
				);

		$found = false;
		foreach($users AS $user)
		{
			if($user->getId() == $u->getId())
			{
				$found = true;
			}
		}
		$this->assertNotEmpty($found);
		$this->assertEquals(1, $users->getTotalCount());

		/* Role creation. */
		$r = new Role();
		$r->role_name = 'JD Role';

		$u->roles[] = $r;

		$this->assertTrue($u->save());

		/* Check M2M filter */
		$users = User::all()->filter('roles', '=', $r->getId());

		$found = false;
		foreach($users AS $user)
		{
			if($user->getId() == $u->getId())
			{
				$found = true;
			}
		}
		$this->assertTrue($found);
		$this->assertEquals(1, $users->getTotalCount());
	}

	public function testFilterMany2OneByID()
	{
		/* Create user. */
		$u = new User();

		/* Set and check storage. */
		$u->firstname = 'Jean3';
		$this->assertEquals('Jean3', $u->firstname);

		$u->lastname = 'Dupont3';
		$this->assertEquals('Dupont3', $u->lastname);

		$u->mail = 'jean.dupont3@test.com';
		$this->assertEquals('jean.dupont3@test.com', $u->mail);

		$u->password = 'My Password';
		$this->assertEquals('', $u->password);

		$this->assertTrue($u->save());

		/* Role creation. */
		$r = new Role();
		$r->role_name = 'JD Role2';

		/* UserRole. */
		$ur = new UserRole();

		$ur->user = $u;
		$ur->role = $r;

		$this->assertTrue($ur->save());

		$userrole = UserRole::all()->filter('user_id', '=', $u->getId())->fetch();

		$this->assertEquals(1, $userrole->getTotalCount());

		$ur_filtered = $userrole->current();

		$this->assertEquals($u->getId(), $ur_filtered->getId('user_id'));
		$this->assertEquals($r->getId(), $ur_filtered->getId('role_id'));
	}

	public function testFilterMany2OneByObject()
	{
		/* Create user. */
		$u = new User();

		/* Set and check storage. */
		$u->firstname = 'Jean3';
		$this->assertEquals('Jean3', $u->firstname);

		$u->lastname = 'Dupont3';
		$this->assertEquals('Dupont3', $u->lastname);

		$u->mail = 'jean.dupont3@test.com';
		$this->assertEquals('jean.dupont3@test.com', $u->mail);

		$u->password = 'My Password';
		$this->assertEquals('', $u->password);

		$this->assertTrue($u->save());

		/* Role creation. */
		$r = new Role();
		$r->role_name = 'JD Role2';

		/* UserRole. */
		$ur = new UserRole();

		$ur->user = $u;
		$ur->role = $r;

		$this->assertTrue($ur->save());
/*
		$userrole = UserRole::all()->filter(array(
							array('user', '=', $u)
						))->fetch();

		$this->assertEquals(1, $userrole->getTotalCount());

		$ur_filtered = $userrole->current();

		$this->assertEquals($u->getId(), $ur_filtered->getId('user_id'));
		$this->assertEquals($r->getId(), $ur_filtered->getId('role_id'));
*/
	}

	public function testFilterMany2OneByField()
	{
		/* Create user. */
		$u = new User();

		/* Set and check storage. */
		$u->firstname = 'Jean4';
		$this->assertEquals('Jean4', $u->firstname);

		$u->lastname = 'Dupont4';
		$this->assertEquals('Dupont4', $u->lastname);

		$u->mail = 'jean.dupont4@test.com';
		$this->assertEquals('jean.dupont4@test.com', $u->mail);

		$u->password = 'My Password';
		$this->assertEquals('', $u->password);

		$this->assertTrue($u->save());

		/* Role creation. */
		$r = new Role();
		$r->role_name = 'JD Role2';

		/* UserRole. */
		$ur = new UserRole();

		$ur->user = $u;
		$ur->role = $r;

		$this->assertTrue($ur->save());

		$userrole = UserRole::all()->filter('user.user_id', '=', $u->getId())->fetch();

		$this->assertEquals(1, $userrole->getTotalCount());

		$ur_filtered = $userrole->current();

		$this->assertEquals($u->getId(), $ur_filtered->getId('user_id'));
		$this->assertEquals($r->getId(), $ur_filtered->getId('role_id'));

	}

	public function testFilterContains()
	{
		/* Role creation. */
		$r1 = new Role();
		$r1->role_name = 'AABBCC';
		$this->assertTrue($r1->save());

		$r2 = new Role();
		$r2->role_name = 'BBCCDD';
		$this->assertTrue($r2->save());

		$r3 = new Role();
		$r3->role_name = 'CCDDAA';
		$this->assertTrue($r3->save());

		$r4 = new Role();
		$r4->role_name = 'DDAABB';
		$this->assertTrue($r4->save());

		/**
		 * Search for a specific one.
		 */
		$role = Role::all()->filter('role_name', 'like', 'AABBCC')->fetch();
		$this->assertEquals(1, $role->getTotalCount());

		$r = $role->current();
		$this->assertEquals($r1->getId(), $r->getId());

		/**
		 * Beginning with A
		 */
		$role = Role::all()->filter('role_name', 'like', 'AA%')->fetch();
		$this->assertEquals(1, $role->getTotalCount());

		$r = $role->current();
		$this->assertEquals($r1->getId(), $r->getId());

		/**
		 * Ending with A
		 */
		$role = Role::all()->filter('role_name', 'like', '%AA')->fetch();
		$this->assertEquals(1, $role->getTotalCount());

		$r = $role->current();
		$this->assertEquals($r3->getId(), $r->getId());

		/**
		 * Containing A
		 */
		$role = Role::all()->filter('role_name', 'like', '%AA%')->fetch();
		$this->assertEquals(3, $role->getTotalCount());

		// TODO: Finish the test and try to avoid the conflict with existings roles.
		//$r = $role->current();
		//$this->assertEquals($r1->getId(), $r->getId());
	}
}

<?php

use Biome\Core\Controller\ObjectControllerTrait;

class RoleController extends BaseController
{
	use ObjectControllerTrait;

	public function objectName()
	{
		return 'Role';
	}

	public function collectionName()
	{
		return 'Roles';
	}

	public function postAddUser($role_id)
	{
		$user_id = $this->request()->get('user_id');

		$user = User::get($user_id);
		$role = Role::get($role_id);

		$user->roles[] = $role;

		if($user->save())
		{
			$this->flash()->success('User added!');
		}

		return $this->response()->redirect();
	}

	public function postAuthorizations(RolesCollection $c)
	{
		$r = \Biome\Biome::getService('request');
		$rights = $r->get('rights');

		$c->role->role_rights = json_encode($rights);

		if($c->role->save())
		{
			$this->flash()->success('Role authorizations updated!');
		}
		else
		{
			$this->flash()->error('Unable to update the role authorizations!', join(', ', $c->role->getErrors()));
		}
		return $this->response()->redirect();
	}
}

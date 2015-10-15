<?php

class RoleController extends BaseController
{
	public function postCreate(Role $r)
	{
		if($r->save())
		{
			$this->flash()->success('Role created!');
		}
		else
		{
			$this->flash()->error('Unable to create the role!');
		}
		return $this->response()->redirect();
	}

	public function postEdit(RolesCollection $c)
	{
		if($c->role->save())
		{
			$this->flash()->success('Role updated!');
		}
		else
		{
			$this->flash()->error('Unable to update the role!', join(', ', $c->role->getErrors()));
		}

		return $this->response()->redirect();
	}

	public function getIndex() { }

	public function getCreate() { }

	public function getEdit($role_id)
	{
		$c = RolesCollection::get();
		$c->role->sync($role_id);
	}

	public function getDelete($role_id)
	{
		$role = Role::get($role_id);

		if($role->delete())
		{
			$this->flash()->success('Role deleted!');
		}
		else
		{
			$this->flash()->error('Unable to delete the role!');
		}

		return $this->response()->redirect();
	}
}

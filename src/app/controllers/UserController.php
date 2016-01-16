<?php

class UserController extends BaseController
{
	public function postSave(AuthCollection $c)
	{
		if(!$c->user->save())
		{
			$this->flash()->error('@string/profile_update_failure');
			return $this->response()->redirect();
		}

		$this->flash()->success('@string/profile_update_success');

		return $this->response()->redirect();
	}

	public function postUpdate_password(AuthCollection $c)
	{
		if($c->password != $c->password_confirm)
		{
			$this->flash()->error('@string/password_mismatch');
			return $this->response()->redirect();
		}

		$c->user->password = $c->password;
		$c->user->save();

		$this->flash()->success('@string/password_update_success');

		return $this->response()->redirect();
	}

	public function postCreate(User $u)
	{
		if($u->save())
		{
			$this->flash()->success('@string/user_create_success');
		}
		else
		{
			$this->flash()->error('@string/user_create_failure');
		}
		return $this->response()->redirect();
	}

	public function postEdit(UsersCollection $c)
	{
		if(!empty($c->password) || !empty($c->password_confirm))
		{
			if($c->password != $c->password_confirm)
			{
				$this->flash()->error('@string/password_mismatch');
				return $this->response()->redirect();
			}
			else
			{
				$c->user->password = $c->password;
			}
		}

		if($c->user->save())
		{
			$this->flash()->success('@string/user_update_success');
		}
		else
		{
			$this->flash()->error('@string/user_update_failure', join(', ', $c->user->getErrors()));
		}

		return $this->response()->redirect();
	}

	public function getIndex() { }

	public function getCreate() { }

	public function getProfile() { }

	public function getShow($user_id)
	{
		$this->view->user = User::get($user_id);
	}

	public function getRemoveRole($user_id, $role_id)
	{
		$userroles = UserRole::find(
					array('user_id', '=', $user_id),
					array('role_id', '=', $role_id)
				);

		foreach($userroles AS $ur)
		{
			if($ur->delete())
			{
				$this->flash()->success('@string/user_role_remove_success');
			}
		}
		return $this->response()->redirect();
	}

	public function postAddRole($user_id)
	{
		$user = User::get($user_id);

		if(count($user->roles) > 0)
		{
			$this->flash()->error('@string/user_role_limit_reached');
			return $this->response()->redirect();
		}

		$role_id = $this->request()->get('role_id');

		$role = Role::get($role_id);

		$user->roles[] = $role;

		if($user->save())
		{
			$this->flash()->success('@string/user_role_add_success');
		}

		return $this->response()->redirect();
	}

	public function getDelete($user_id)
	{
		$user = User::get($user_id);

		if($user->delete())
		{
			$this->flash()->success('@string/user_delete_success');
		}
		else
		{
			$this->flash()->error('@string/user_delete_failure');
		}

		return $this->response()->redirect();
	}
}

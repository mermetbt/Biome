<?php

class UserController extends BaseController
{
	public function getProfile() { }

	public function postSave(AuthCollection $c)
	{
		if(!$c->user->save())
		{
			$this->flash()->error('Update failure!');
			return $this->response()->redirect();
		}

		$this->flash()->success('Profile updated!');

		return $this->response()->redirect();
	}

	public function postUpdate_password(AuthCollection $c)
	{
		if($c->password != $c->password_confirm)
		{
			$this->flash()->error('Password doesn\'t match!');
			return $this->response()->redirect();
		}

		$c->user->password = $c->password;
		$c->user->save();

		$this->flash()->success('Password updated!');

		return $this->response()->redirect();
	}

	public function postCreate(User $u)
	{
		if($u->save())
		{
			$this->flash()->success('User created!');
		}
		else
		{
			$this->flash()->error('Unable to create the user!');
		}
		return $this->response()->redirect();
	}

	public function postEdit(UsersCollection $c)
	{
		if(!empty($c->password) || !empty($c->password_confirm))
		{
			if($c->password != $c->password_confirm)
			{
				$this->flash()->error('Password doesn\'t match!');
				return $this->response()->redirect();
			}
			else
			{
				$c->user->password = $c->password;
			}
		}

		if($c->user->save())
		{
			$this->flash()->success('User updated!');
		}
		else
		{
			$this->flash()->error('Unable to update the user!', join(', ', $c->user->getErrors()));
		}

		return $this->response()->redirect();
	}

	public function postUpdate(UsersCollection $c)
	{
		if($c->password != $c->password_confirm)
		{
			$this->flash()->error('Password doesn\'t match!');
			return $this->response()->redirect();
		}

		$c->user->password = $c->password;
		if($c->user->save())
		{
			$this->flash()->success('Password updated!');
		}
		else
		{
			$this->flash()->error('Unable to update the user!', join(', ', $c->user->getErrors()));
		}

		return $this->response()->redirect();
	}

	public function getIndex() { }

	public function getCreate() { }

	public function getEdit($user_id)
	{
		$c = UsersCollection::get();
		$c->user->sync($user_id);
	}

	public function getDelete($user_id)
	{
		$user = User::get($user_id);

		if($user->delete())
		{
			$this->flash()->success('User deleted!');
		}
		else
		{
			$this->flash()->error('Unable to delete the user!');
		}

		return $this->response()->redirect();
	}
}

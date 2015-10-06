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

	public function getIndex() { }

	public function getEdit($user_id)
	{
		$u = User::get($user_id);
		$this->view->user = $u;
	}
}

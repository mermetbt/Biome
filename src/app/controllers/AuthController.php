<?php

use Biome\Core\Controller;
use Biome\Core\Collection;

class AuthController extends Controller
{
	public function getLogin()
	{
		return $this->response()->redirect();
	}

	public function getSignin()
	{
		return $this->getLogin();
	}

	public function getSignup()
	{
		return $this->response()->redirect();
	}

	public function postLogin(AuthCollection $c)
	{
		if(!$c->user->validate('mail', 'password'))
		{
			$this->flash()->error('@string/invalid_mail_or_password');
			return $this->response()->redirect();
		}

		$result = $c->user->fetch('mail', 'password');
		if(!$c->isAuthenticated())
		{
			$this->flash()->error('@string/authentication_failure');
		}

		return $this->response()->redirect();
	}

	public function postSignin(AuthCollection $c)
	{
		return $this->postLogin($c);
	}

	public function postSignup(User $user)
	{
		if(!$user->save())
		{
			return $this->response()->redirect();
		}

		$this->flash()->success('@string/user_register_success');

		return $this->response()->redirect();
	}

	public function getLogout()
	{
		$c = Collection::get('auth');
		$c->logout();

		$this->flash()->success('@string/user_logout_success');

		return $this->response()->redirect('');
	}
}

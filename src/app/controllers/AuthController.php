<?php

use Biome\Core\Controller;
use Biome\Core\Collection;

class AuthController extends Controller
{
	public function getLogin()
	{
		return $this->response()->redirect();
	}

	public function getSignup()
	{
		return $this->response()->redirect();
	}

	public function postLogin(AuthCollection $c)
	{
		if(!$c->user->validate('mail', 'password'))
		{
			return $this->response()->redirect();
		}

		$result = $c->user->fetch('mail', 'password');
		if(!$c->isAuthenticated())
		{
			$this->flash()->error('Authentication failed!');
		}

		return $this->response()->redirect();
	}

	public function postSignup(User $user)
	{
		if(!$user->save())
		{
			return $this->response()->redirect();
		}

		$this->flash()->success('User registered!');

		return $this->response()->redirect();
	}

	public function getLogout()
	{
		$c = Collection::get('auth');
		$c->logout();

		$this->flash()->success('Good Bye!');

		return $this->response()->redirect('');
	}
}

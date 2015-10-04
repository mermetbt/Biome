<?php

use Biome\Core\Controller;
use Biome\Core\Collection;

class AuthController extends Controller
{
	public function getLogin()
	{
		return $this->response()->redirect();
	}

	public function postLogin(AuthCollection $c)
	{
		$result = $c->user->fetch('mail', 'password');
		if($result === NULL)
		{
			echo 'No result! <br/>';
		}

		if($c->isAuthenticated())
		{
			echo 'User authenticated! <br/>';
			echo 'Authentication of ', $c->user->firstname, ' ', $c->user->lastname, '<br/>';
			echo 'Id:', $c->user->getId(), '<br/>';
		}
		else
		{
			echo 'Authentication failed! <br/>';
		}

		echo '<a href="', URL::fromRoute(),'">Go back!</a>';
	}

	public function getSignup()
	{
		return $this->response()->redirect();
	}

	public function postSignup(AuthCollection $c)
	{
// 		if(empty($c->user->mail))
// 		{
// 			$msg = print_r($this->request()->request, true);
// 			throw new Exception($msg);
// 		}

		echo 'New mail set to ', $c->user->mail, '<br/>';
		$c->storeUser($c->user);
		echo '<a href="', URL::fromRoute(),'">Go back!</a>';
	}

	public function postSignup_obj(User $u)
	{
		echo 'New user set to ', $u->firstname, ' ', $u->lastname, '<br/>';

		$c = Collection::get('auth');
		$c->storeUser($u);

		$u->save();

		echo 'New user set to ', $u->firstname, ' ', $u->lastname, '<br/>';

		echo '<a href="', URL::fromRoute(),'">Go back!</a>';
	}

	public function getLogout()
	{
		$c = Collection::get('auth');
		$c->logout();

		return $this->response()->redirect();
	}
}

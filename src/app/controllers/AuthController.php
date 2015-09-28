<?php

use Biome\Core\Controller;
use Biome\Core\Collection;

class AuthController extends Controller
{
	public function getLogin()
	{
		//TODO: Redirect to the main page
	}

	public function postLogin(AuthCollection $c)
	{
		echo 'Authentication of ', $c->user->firstname, ' ', $c->user->lastname, '<br/>';
		echo '<a href="', URL::fromRoute(),'">Go back!</a>';
	}

	public function getSignup()
	{
		//TODO: Redirect to the main page
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

		echo '<a href="', URL::fromRoute(),'">Go back!</a>';
	}
}

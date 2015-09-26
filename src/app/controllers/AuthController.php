<?php

use Biome\Core\Controller;
use Biome\Core\Collection;

class AuthController extends Controller
{
	public function getLogin()
	{
		//TODO: Redirect to the main page
	}

	public function postLogin()
	{
		$c = Collection::get('auth');
		$c->user->username = $this->request()->get('auth_user_username');
	}

	public function getSignup()
	{
		//TODO: Redirect to the main page
	}

	public function postSignup()
	{
		$c = Collection::get('auth');
		$c->user->username = $this->request()->get('auth_user_username');
	}
}

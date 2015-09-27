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

	}

	public function getSignup()
	{
		//TODO: Redirect to the main page
	}

	public function postSignup(AuthCollection $c)
	{
		echo 'New username set to ', $c->user->username, '<br/>';
	}
}

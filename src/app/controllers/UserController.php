<?php

use Biome\Core\Controller;
use Biome\Core\Collection;

class UserController extends Controller
{
	public function getProfile()
	{
	}

	public function postSave(AuthCollection $c)
	{
		$c->user->save();
		return $this->response()->redirect();
	}
}

<?php

use Biome\Core\Controller;
use Biome\Core\Collection;

class BaseController extends Controller
{
	public function preRoute()
	{
		/**
		 * Check authentication.
		 */
		$auth = Collection::get('auth');
		if(!$auth->isAuthenticated())
		{
			$this->response()->redirect('');
			return FALSE;
		}

		return TRUE;
	}
}

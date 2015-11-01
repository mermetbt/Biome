<?php

use Biome\Core\Controller;
use Biome\Core\Collection;

class BaseController extends Controller
{
	public function beforeRoute()
	{
		/**
		 * Check route rights.
		 */
		$this->checkAuthorizations();

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

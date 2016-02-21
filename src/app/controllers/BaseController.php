<?php

use Biome\Core\Controller;
use Biome\Core\Collection;

class BaseController extends Controller
{
	public function beforeRoute()
	{
		/**
		 * Check authentication.
		 */
		$auth = Collection::get('auth');
		if(!$auth->isAuthenticated())
		{
			$this->flash()->error('@string/user_not_authenticated');
			$this->response()->redirect('');
			return FALSE;
		}

		/**
		 * Check route rights.
		 */
		try
		{
			$this->checkAuthorizations();
		} catch(ForbiddenException $e)
		{
			$this->flash()->error('@string/user_forbidden');
			$this->response()->redirect('');
			return FALSE;
		}

		return TRUE;
	}
}

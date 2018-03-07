<?php

namespace Biome\Core;

class Auth {
	public static function isAdmin()
	{
		$auth = \Biome\Core\Collection::get('auth');
		if($auth->isAuthenticated())
		{
			$admin_id = 1; // Default value of the Admin role id.
			if(\Biome\Biome::hasService('config'))
			{
				$admin_id = \Biome\Biome::getService('config')->get('ADMIN_ROLE_ID', 1);
			}
			$roles = $auth->user->roles;
			foreach($roles AS $role)
			{
				/* If Admin. */
				if($role->role_id == $admin_id)
				{
					return TRUE;
				}
			}
		}
		return FALSE;
	}
}

<?php

use Biome\Core\Controller\ObjectControllerTrait;

class RoleController extends BaseController
{
	use ObjectControllerTrait;

	public function objectName()
	{
		return 'Role';
	}

	public function collectionName()
	{
		return 'Roles';
	}
}

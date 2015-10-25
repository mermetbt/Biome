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

	public function getAdd($role_id)
	{
		//$c = Collection::get($collection_name);
		//$c->$object_name->sync($object_id);

		\Biome\Core\ORM\ObjectLoader::load('role');
		$role = Role::get($role_id);
		$this->view->role = $role;
	}

	public function postAdd($role_id)
	{
		
	}
}

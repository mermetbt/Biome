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

		$role = Role::get($role_id);
		$this->view->role = $role;
	}

	public function postAdd($role_id)
	{

	}

	public function postAuthorizations(RolesCollection $c)
	{
		$r = \Biome\Biome::getService('request');
		$objects = $r->get('objects');

		$c->role->role_rights = json_encode($objects);

		if($c->role->save())
		{
			$this->flash()->success('Role authorizations updated!');
		}
		else
		{
			$this->flash()->error('Unable to update the role authorizations!', join(', ', $c->role->getErrors()));
		}
		return $this->response()->redirect();
	}
}

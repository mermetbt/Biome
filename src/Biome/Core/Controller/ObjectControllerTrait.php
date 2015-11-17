<?php

namespace Biome\Core\Controller;

use Biome\Core\Controller;
use Biome\Core\Collection;
use Biome\Core\ORM\Models;
use Biome\Core\ORM\ObjectLoader;

trait ObjectControllerTrait
{
	public function getIndex() { }
	public function getCreate() { }
	public function getEdit($object_id)
	{
		$collection_name = $this->collectionName();
		$object_name = strtolower($this->objectName());
		$c = Collection::get($collection_name);
		$c->$object_name->sync($object_id);
	}

	public function getShow($object_id)
	{
		$collection_name = $this->collectionName();
		$object_name = strtolower($this->objectName());
		$c = Collection::get($collection_name);
		$c->$object_name->sync($object_id);
	}

	public function getDelete($object_id)
	{
		$object_name = $this->objectName();
		ObjectLoader::load($object_name);
		$object = $object_name::get($object_id);

		if($object->delete())
		{
			$this->flash()->success($object_name . ' deleted!');
			return $this->response()->redirect(substr(strtolower(get_called_class()), 0, -strlen('Controller')));
		}

		$this->flash()->error('Unable to delete the ' . $object_name . '!');
		return $this->response()->redirect();
	}

	/**
	 * POST request for creating a new object.
	 */
	public function postCreate(Models $object)
	{
		$object_name = $this->objectName();
		if($object->save())
		{
			$this->flash()->success($object_name . ' created!');
			return $this->response()->redirect(strtolower($object_name));
		}

		$this->flash()->error('Unable to create the ' . $object_name . '!', join(', ', $object->getErrors()));

		return $this->response()->redirect();
	}

	public function postEdit(Collection $collection)
	{
		$object_name = strtolower($this->objectName());
		if($collection->$object_name->save())
		{
			$this->flash()->success($object_name . ' updated!');
		}
		else
		{
			$this->flash()->error('Unable to update the ' . $object_name . '!', join(', ', $collection->$object_name->getErrors()));
		}

		return $this->response()->redirect();
	}

	public function postDelete(Models $object)
	{

	}

	public abstract function objectName();
	public abstract function collectionName();

}

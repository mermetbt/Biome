<?php

namespace Biome\Core\Rights;

class FreeRights implements RightsInterface
{
	public function exportToJSON()
	{
		return NULL;
	}

	public function setRoute($method, $controller, $action, $allowed = TRUE)
	{
	}

	public function setObject($object_name, $view = TRUE, $create = TRUE, $edit = TRUE, $delete = TRUE)
	{
	}

	public function setAttribute($object_name, $attribute_name, $view = TRUE, $edit = TRUE)
	{
	}

	public function isRouteAllowed($method, $controller, $action)
	{
		return TRUE;
	}

	public function isUrlAllowed($method, $url)
	{
		return TRUE;
	}

	public function isObjectView($object_name)
	{
		return TRUE;
	}

	public function isObjectEdit($object_name)
	{
		return TRUE;
	}

	public function isObjectCreate($object_name)
	{
		return TRUE;
	}

	public function isObjectDelete($object_name)
	{
		return TRUE;
	}

	public function isAttributeView($object_name, $attribute_name = '')
	{
		return TRUE;
	}

	public function isAttributeEdit($object_name, $attribute_name = '')
	{
		return TRUE;
	}
}

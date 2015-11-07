<?php

namespace Biome\Core\Rights;

interface RightsInterface
{
	public function setRoute($method, $controller, $action, $allowed = TRUE);

	public function setObject($object_name, $view = TRUE, $create = TRUE, $edit = TRUE, $delete = TRUE);

	public function setAttribute($object_name, $attribute_name, $view = TRUE, $edit = TRUE);

	public function isRouteAllowed($method, $controller, $action);

	public function isUrlAllowed($method, $url);

	public function isObjectView($object_name);

	public function isObjectEdit($object_name);

	public function isObjectCreate($object_name);

	public function isObjectDelete($object_name);

	public function isAttributeView($object_name, $attribute_name = '');

	public function isAttributeEdit($object_name, $attribute_name = '');

}

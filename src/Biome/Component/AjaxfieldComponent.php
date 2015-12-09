<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\ORM\AbstractField;
use Biome\Core\HTTP\Request;

use Biome\Core\ORM\Field\Many2OneField;

class AjaxfieldComponent extends VariableComponent
{
	public function render()
	{
		$this->addClasses('form-control');
		return parent::render();
	}

	public function getPlaceholder()
	{
		return $this->getAttribute('placeholder', '');
	}

	public function getObject(&$last = NULL)
	{
		$value_name = $this->getAttribute('value');
		$variables = $this->fetchVariables($value_name);

		$variable = reset($variables);

		$raw = explode('.', $variable);
		$last = array_pop($raw);

		$variable = join('.', $raw);
		$object = $this->fetchValue('#{' . $variable . '}');

		if(!$object instanceof \Biome\Core\ORM\Models)
		{
			throw new \Exception('Unable to fetch the object from the variable: ' . $variable);
		}
		return $object;
	}

	public function handleAjaxRequest(Request $request)
	{
		$action = $request->get('action');
		switch($action)
		{
			case 'search':
				$results = $this->searchValue($request);
				break;
			case 'save':
				$results = $this->saveValue($request);
				break;
			default:
				$results = array('error' => 'Unrecognized action!');
		}
		echo json_encode($results);
		return TRUE;
	}

	protected function searchValue(Request $request)
	{
		$q = $request->get('q');
		$page = $request->get('page');

		$field = $this->getField();

		if(!$field instanceof Many2OneField)
		{
			throw new \Exception('Unable to do an ajax search on a non many2one field!');
		}

		$object_name = $field->getObjectName();
		$queryset = $object_name::all();

		if(empty($page))
		{
			$page = 1;
		}

		$offset = ($page - 1);
		$limit = $page * 30;

		$queryset->limit($offset, $limit);

		$search_field = $field->getSearchField();

		if(empty($search_field))
		{
			$obj = $field->object();
			if(method_exists($obj, 'getSearchField'))
			{
				$search_field = $obj->getSearchField();
			}
		}

		if(!empty($search_field) && !empty($q))
		{
			$queryset->filter($search_field, 'like', $q . '%');
		}

		$results = array();

		$items = array();
		$results['total_count'] = $queryset->getTotalCount();

		foreach($queryset AS $obj)
		{
			$items[] = array('id' => $obj->getId(), 'name' => (string)$obj);
		}

		$results['items'] = $items;

		$results['incomplete_results'] = count($items) != $results['total_count'];

		$results['object'] = $object_name;

		return $results;
	}

	protected function saveValue(Request $request)
	{
		$last = NULL;
		$object = $this->getObject($last);

		$name = $this->getName();
		if($object->$last instanceof \Biome\Core\ORM\Models)
		{
			$model = $last;
			$last .= '_id';
		}
		else
		{
			$model = substr($last, 0, -3);
		}

		$new_value = $request->get($name);
		if(!empty($new_value))
		{
			$object->$last = $new_value;
		}
		else
		{
			$object->$last = NULL;
		}

		$results = array();
		if($object->save())
		{
			$new_value = $object->$last;
			$results['success'] = 'Success!';
			$results['value'] = empty($new_value) ? '' : $new_value;
			if($object->hasField($model))
			{
				$results['content'] = (string)$object->$model;
			}
		}
		else
		{
			$results['errors'] = $object->getErrors();
		}
		return $results;
	}
}

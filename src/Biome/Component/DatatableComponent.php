<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\ORM\QuerySet;
use Biome\Core\HTTP\Request;

class DatatableComponent extends Component
{
	public function render()
	{
		$this->addClasses('table table-striped table-hover biome-datatable');
		return parent::render();
	}

	public function getVar()
	{
		return $this->attributes['var'];
	}

	public function getValue()
	{
		$value = $this->fetchValue($this->attributes['value']);
		if(!$value instanceof QuerySet && !is_array($value))
		{
			throw new \Exception(	'Unable to loop on a value which is not a QuerySet or an array! '.
									'Value: ' . $this->attributes['value'] . ' '.
									'Result: ' . var_export($value)
			);
		}
		return $value;
	}

	public function handleAjaxRequest(Request $request)
	{
		$var			= $this->getVar();
		$column_list	= $this->getChildren('column');
		$object_list	= $this->getValue();

		if($object_list instanceof QuerySet)
		{
			$object_list->limit($request->get('start'), $request->get('length'));
		}

		$data = array();
		foreach($object_list AS $v)
		{
			$this->setContext($var, $v);

			$item = array();
			foreach($column_list AS $column)
			{
				$item[] = $column->render();
			}

			$data[] = $item;
		}

		$results = array(
			'draw' => (int)$request->get('draw'),
			'recordsTotal' => $object_list->getTotalCount(),
			'recordsFiltered' => $object_list->getTotalCount(),
			'data' => $data
		);

		echo json_encode($results);

		return TRUE;
	}
}

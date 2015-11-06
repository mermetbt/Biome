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
		return $this->getAttribute('var');
	}

	public function getValue()
	{
		$value_name = $this->getAttribute('value');
		$value = $this->fetchValue($value_name);
		if(!$value instanceof QuerySet && !is_array($value))
		{
			throw new \Exception(	'Unable to loop on a value which is not a QuerySet or an array! '.
									'Value: ' . $value_name . ' '.
									'Result: ' . var_export($value)
			);
		}
		return $value;
	}

	public function isOrderable()
	{
		return $this->getAttribute('orderable', FALSE) == 1;
	}

	public function isSearchable()
	{
		return $this->getAttribute('searchable', FALSE) == 1;
	}

	public function hasPaging()
	{
		return $this->getAttribute('pagination', TRUE) == 1;
	}

	public function handleAjaxRequest(Request $request)
	{
		$var			= $this->getVar();
		$column_list	= $this->getChildren('column');
		$object_list	= $this->getValue();

		if($object_list instanceof QuerySet)
		{
			$length = $request->get('length');
			if($length > 0)
			{
				$object_list->limit($request->get('start'), $length);
			}
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

		if($object_list instanceof QuerySet)
		{
			$recordsTotal = $object_list->getTotalCount();
			$recordsFiltered = $recordsTotal;
		}
		else
		{
			$recordsTotal = count($data);
			$recordsFiltered = $recordsTotal;
		}

		$results = array(
			'draw' => (int)$request->get('draw'),
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		);

		echo json_encode($results);

		return TRUE;
	}
}

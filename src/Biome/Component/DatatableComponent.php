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

	public function isOrderable()
	{
		if(!isset($this->attributes['orderable']))
		{
			return FALSE;
		}
		return $this->attributes['orderable'] == 1;
	}

	public function isSearchable()
	{
		if(!isset($this->attributes['searchable']))
		{
			return FALSE;
		}
		return $this->attributes['searchable'] == 1;
	}

	public function hasPaging()
	{
		if(!isset($this->attributes['pagination']))
		{
			return TRUE;
		}
		return $this->attributes['pagination'] == 1;
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

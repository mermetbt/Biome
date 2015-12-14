<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class ColumnComponent extends Component
{
	/**
	 * Return the column name for the filter.
	 */
	public function getName()
	{
		$search = $this->getAttribute('search', FALSE);

		if(empty($search))
		{
			return $this->getTitle();
		}
		$variables = $this->fetchVariables($search);
		return reset($variables);
	}

	public function isSearchable()
	{
		if($this->getAttribute('search', FALSE))
		{
			return TRUE;
		}
		return FALSE;
	}

	public function isOrderable()
	{
		return $this->getAttribute('order', FALSE);
	}

	/**
	 * Return the column header.
	 */
	public function getTitle()
	{
		return $this->getAttribute('headerTitle', '');
	}
}

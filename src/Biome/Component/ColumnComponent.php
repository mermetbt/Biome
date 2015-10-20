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
		return $this->getTitle();
	}

	public function isSearchable()
	{
		return FALSE;
	}

	public function isOrderable()
	{
		return FALSE;
	}

	/**
	 * Return the column header.
	 */
	public function getTitle()
	{
		if(!isset($this->attributes['headerTitle']))
		{
			return '';
		}
		return $this->attributes['headerTitle'];
	}
}

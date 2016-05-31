<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class ConditionComponent extends Component
{
	public function isValid()
	{
		$str_condition = $this->getAttribute('if', NULL);
		if($str_condition === NULL)
		{
			$ifset_condition = $this->getAttribute('ifset', NULL);
			if($ifset_condition === NULL)
			{
				return FALSE;
			}
			$data = $this->fetchValue($ifset_condition);
			$phpEval = 'return !empty($data);';
			return eval($phpEval);
		}

		$condition = $this->fetchValue($str_condition);
		$phpEval = 'return (' . (empty($condition) ? 'FALSE' : $condition) . ') == TRUE;';
		return eval($phpEval);
	}
}

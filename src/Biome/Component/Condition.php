<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class Condition extends Component
{
	public function isValid()
	{
		$str_condition = $this->attributes['if'];
		$condition = $this->fetchValue($str_condition);
		return eval('return (' . (empty($condition) ? 'FALSE' : $condition) . ') == TRUE;');
	}
}

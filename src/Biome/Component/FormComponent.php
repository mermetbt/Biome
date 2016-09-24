<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class FormComponent extends Component
{
	public function getAction()
	{
		$controller	= $this->getAttribute('controller', NULL);
		$action		= $this->getAttribute('action', NULL);
		if(!empty($controller) && !empty($action))
		{
			return \URL::fromRoute($controller, $action);
		}
		return NULL;
	}

	public function getEnctype()
	{
		$enctype = $this->getAttribute('enctype', NULL);
		return $enctype;
	}
}

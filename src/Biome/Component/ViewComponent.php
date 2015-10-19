<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class ViewComponent extends Component
{
	public function getAction()
	{
		if(!isset($this->attributes['action']))
		{
			return NULL;
		}
		return $this->attributes['action'];
	}

	public function getCss()
	{
		if(!isset($this->attributes['css']))
		{
			return array();
		}

		$css_list = explode(',', $this->attributes['css']);
		foreach($css_list AS $i => $c)
		{
			$css_list[$i] = trim($c);
		}

		return $css_list;
	}
}

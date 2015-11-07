<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class ViewComponent extends Component
{
	public function getAction()
	{
		return $this->getAttribute('action');
	}

	public function getCss()
	{
		$css = $this->getAttribute('css', '');

		$css_list = explode(',', $css);
		foreach($css_list AS $i => $c)
		{
			$css = trim($c);
			$css_list[$i] = $css;
			if(empty($css))
			{
				unset($css_list[$i]);
			}
		}

		return $css_list;
	}
}

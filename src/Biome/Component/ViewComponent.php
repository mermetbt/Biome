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
			$css_list[$i] = trim($c);
		}

		return $css_list;
	}
}

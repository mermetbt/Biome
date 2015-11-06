<?php

namespace Biome\Component;

use Biome\Core\View\Component;

class ViewsComponent extends Component
{
	protected $_css_list = array();

	public function load($action)
	{
		$css_list = array();
		foreach($this->_value AS $index => $v)
		{
			if($v instanceof ViewComponent)
			{
				if($v->getAction() != $action)
				{
					unset($this->_value[$index]);
				}
				else
				{
					$css_list = array_merge($css_list, $v->getCss());
				}
			}
		}
		$this->_css_list = $css_list;
	}

	public function getCss()
	{
		return $this->_css_list;
	}
}

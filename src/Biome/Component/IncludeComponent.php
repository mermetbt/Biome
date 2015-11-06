<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\View\TemplateReader;

class IncludeComponent extends Component
{
	public function building()
	{
		$filename = $this->getAttribute('src');

		$dirs = \Biome\Biome::getDirs('views');
		$template_file = '';
		foreach($dirs AS $dir)
		{
			$path = $dir . '/' . $filename;
			if(!file_exists($path))
			{
				continue;
			}
			$template_file = $path;
		}

		if(!file_exists($template_file))
		{
			throw new \Exception('Unable to load template file: ' . $filename);
		}

		$nodes = TemplateReader::loadFilename($template_file);

// 		print_r($nodes);
//
// 		echo '<br/>';
// 		print_r($this->value);
// 		die();

		$this->_value = $nodes['value']->_value;

		return TRUE;
	}
}

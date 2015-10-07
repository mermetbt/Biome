<?php

namespace Biome\Component;

use Biome\Core\View\Component;
use Biome\Core\View\TemplateReader;

class IncludeComponent extends Component
{
	public function building()
	{
		$filename = $this->attributes['src'];

		$dirs = \Biome\Biome::getDirs('views');
		foreach($dirs AS $dir)
		{
			$path = $dir . '/' . $filename;
			if(!file_exists($path))
			{
				continue;
			}
		}

		if(!file_exists($path))
		{
			throw new \Exception('Unable to load template file: ' . $filename);
		}

		$nodes = TemplateReader::loadFilename($path);

// 		print_r($nodes);
//
// 		echo '<br/>';
// 		print_r($this->value);
// 		die();

		$this->value = $nodes['value']->value;

		return TRUE;
	}
}

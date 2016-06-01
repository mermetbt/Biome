<?php

namespace Biome\Core\View;

use Sabre\Xml\Reader;
use Biome\Core\Logger\Logger;

class TemplateReader extends Reader
{
	public static function loadFilename($filename)
	{
		$xml_contents = file_get_contents($filename);
		$reader = new Reader();

		/**
		 * Loading components
		 */
		$components_list = array();
		$components = scandir(__DIR__ . '/../../Component/');
		foreach($components AS $file)
		{
			if($file[0] == '.')
			{
				continue;
			}

			if(substr($file, -4) != '.php')
			{
				continue;
			}

			$componentName = substr($file, 0, -strlen('Component.php'));
			$components_list['{http://github.com/mermetbt/Biome/}' . strtolower($componentName)] = 'Biome\\Component\\'.$componentName.'Component';
		}

		$components_dirs = \Biome\Biome::getDirs('components');
		$components_dirs = array_reverse($components_dirs);
		foreach($components_dirs AS $dir)
		{
			$components = scandir($dir);
			foreach($components AS $file)
			{
				if($file[0] == '.')
				{
					continue;
				}

				if(substr($file, -4) != '.php')
				{
					continue;
				}

				$componentName = substr($file, 0, -strlen('Component.php'));
				$components_list['{http://github.com/mermetbt/Biome/}' . strtolower($componentName)] = $componentName.'Component';
			}
		}

		$reader->elementMap = $components_list;

		/**
		 * Parsing XML template
		 */

		$reader->xml($xml_contents);
		$tree = $reader->parse();

		return $tree;
	}
}

<?php

namespace Biome\Core\View;

use Sabre\Xml\Reader;

class TemplateReader extends Reader
{
	public static function loadFilename($filename)
	{
		$xml_contents = file_get_contents($filename);
		$reader = new Reader();

		/**
		 * Loading components
		 */
		$components = scandir(__DIR__ . '/../../Component/');
		$components_list = array();
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

		$reader->elementMap = $components_list;

		/**
		 * Parsing XML template
		 */

		$reader->xml($xml_contents);
		$tree = $reader->parse();

		return $tree;
	}
}

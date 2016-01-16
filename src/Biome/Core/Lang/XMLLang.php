<?php

namespace Biome\Core\Lang;

use Sabre\Xml\Reader;

class XMLLang implements LangInterface
{
	protected $locales = array();
	protected $patterns = array();

	public function __construct(array $locales = array())
	{
		$this->locales = $locales;
		$this->load();
	}

	protected function load()
	{
		$lang_dirs = \Biome\Biome::getDirs('resources');

		$reverse_locales = array_reverse($this->locales);

		foreach($lang_dirs AS $dir)
		{
			/**
			 * Read default language file.
			 */
			$locale_dir = $dir . '/string/';

			if(!file_exists($locale_dir))
			{
				continue;
			}

			$files = scandir($locale_dir);
			foreach($files AS $file)
			{
				if($file[0] == '.')
				{
					continue;
				}

				if(substr($file, -4) != '.xml')
				{
					continue;
				}

				$xmlFile = $locale_dir . '/' . $file;
				$this->read($xmlFile);
			}

			/**
			 * Read locale language file.
			 */
			foreach($reverse_locales AS $locale)
			{
				$locale_dir = $dir . '/string/' . $locale . '/';

				if(!file_exists($locale_dir))
				{
					continue;
				}

				$files = scandir($locale_dir);
				foreach($files AS $file)
				{
					if($file[0] == '.')
					{
						continue;
					}

					if(substr($file, -4) != '.xml')
					{
						continue;
					}

					$xmlFile = $locale_dir . '/' . $file;
					$this->read($xmlFile);
				}
			}
		}
	}

	protected function read($xmlFile)
	{
		$xml_contents = file_get_contents($xmlFile);
		$reader = new Reader();

		$reader->xml($xml_contents);
		$tree = $reader->parse();

		foreach($tree['value'] AS $node)
		{
			$key = $node['attributes']['name'];
			$value = $node['value'];
			$this->patterns[$key] = $value;
		}
	}

	public function __get($pattern)
	{
		return $this->patterns[$pattern];
	}

	public function get($pattern, array $parameters = array(), $locale = '')
	{
		if(!isset($this->patterns[$pattern]))
		{
			return '@string/' . $pattern;
		}

		$str_pattern = $this->patterns[$pattern];
		if(empty($parameters))
		{
			return $str_pattern;
		}

		return sprintf($pattern, $parameters);
	}
}

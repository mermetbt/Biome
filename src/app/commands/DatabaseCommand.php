<?php

use Biome\Biome;
use Biome\Core\Command\AbstractCommand;
use Biome\Core\ORM\ObjectLoader;
use Biome\Core\ORM\Inspector\SQLModelInspector;

class DatabaseCommand extends AbstractCommand
{
	public function showCreateTable()
	{
		$modelsDirs = Biome::getDirs('models');

		/**
		 * List existings models.
		 */
		$objects = array();
		foreach($modelsDirs AS $dir)
		{
			if(!file_exists($dir))
			{
				continue;
			}
			$filenames = scandir($dir);
			foreach($filenames AS $file)
			{
				if($file[0] == '.')
				{
					continue;
				}

				$object_name = substr($file, 0, -4);
				$objects[$object_name] = $object_name;
			}
		}

		/**
		 * For each models, generate create table.
		 */
		foreach($objects AS $object_name)
		{
			$object = ObjectLoader::get($object_name);

			$sql_inspector = new SQLModelInspector();
			$object->inspectModel($sql_inspector);

			$query = $sql_inspector->generate();
			echo $query, PHP_EOL;
		}
	}
}

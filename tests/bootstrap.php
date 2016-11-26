<?php

/**
 * Activate the autoload.
 */
require_once __DIR__ . '/../vendor/autoload.php';

Biome\Biome::registerDirs(array(
	'commands'		=> __DIR__ . '/../src/app/commands/',
	'controllers'	=> __DIR__ . '/../src/app/controllers/',
	'models'		=> [__DIR__ . '/../src/app/models/', __DIR__ . '/models/'],
	'views'			=> __DIR__ . '/../src/app/views/',
	'components'	=> __DIR__ . '/../src/app/components/',
	'collections'	=> __DIR__ . '/../src/app/collections/',
	'resources'		=> __DIR__ . '/../src/resources/',
), TRUE);

Biome\Biome::registerAlias(array(
	'URL'		=> 'Biome\Core\URL',
));

Biome\Biome::registerService('logger', function() {
	return new \Biome\Core\Logger\Handler\FileLogger(__DIR__ . '/../biome-tests.log');
});

Biome\Biome::registerService('mysql', function() {
	$DB = Biome\Core\ORM\Connector\MySQLConnector::getInstance();
	$DB->setConnectionParameters(array(
			'hostname' => 'localhost',
			'username' => 'test',
			'password' => 'test',
			'database' => 'biome'));

	return $DB;
});

\Biome\Biome::tests();

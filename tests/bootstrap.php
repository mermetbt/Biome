<?php

/**
 * Activate the autoload.
 */
require_once __DIR__ . '/../vendor/autoload.php';

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
			'username' => 'root',
			'password' => '',
			'database' => 'biome'));

	return $DB;
});

\Biome\Biome::tests();

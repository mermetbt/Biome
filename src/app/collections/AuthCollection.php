<?php

use Biome\Core\Collection\SessionCollection;

class AuthCollection extends SessionCollection
{
	protected $map = array(
		'user' => 'User',
	);

}

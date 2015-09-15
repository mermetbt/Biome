<?php

use Biome\Core\Controller;

class IndexController extends Controller
{
	public function getIndex()
	{
		echo 'My main controller !';
		echo '<a href="index/test">Test</a>';
	}

	public function getTest()
	{
		echo 'Test!';
		echo '<a href="../">Go back!</a>';
	}
}

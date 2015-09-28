<?php

use Biome\Core\Controller;

class IndexController extends Controller
{
	public function getIndex()
	{

	}

	public function getUnset()
	{
		if(isset($_SESSION['collections']))
		{
			unset($_SESSION['collections']);
		}

		return $this->response()->redirect();
	}

	public function getTest()
	{

	}
}

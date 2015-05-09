<?php

namespace Doo;

/**
* Autoloader
*/
class Autoload
{
	
	public static function register()
	{

		spl_autoload_register([__CLASS__, "load"]);

	}

	private static function load($class)
	{

		$class = str_replace(__NAMESPACE__ . "\\", "", $class);

		$class = __DIR__ . '/' . $class;

		$class = str_replace("\\", "/", $class);

		require $class . '.php';

	}

}

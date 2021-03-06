<?php
$app->detectEnvironment(function () use ($app) {

	if (!isset($_SERVER['HTTP_HOST'])) {
		$dotenv = new Dotenv\Dotenv($app['path.base'], $app->environmentFile());
		$dotenv->overload(); //this is important
	}

	$pos = mb_strpos($_SERVER['HTTP_HOST'], '.');
	$prefix = '';

	if ($pos) {
		$prefix = mb_substr($_SERVER['HTTP_HOST'], 0, $pos);
	}

	$file = '.' . $prefix . '.env';

	if (!file_exists($app['path.base'] . '/' . $file)) {
		$file = '.env';
	}

	if ( $_SERVER['HTTP_HOST'] == "127.0.0.1:8000" or $_SERVER['HTTP_HOST'] == "localhost:9000" ){
		$file = '.guataqui.env';
	}

	$dotenv = new Dotenv\Dotenv($app['path.base'], $file);
	$dotenv->overload(); //this is important

});

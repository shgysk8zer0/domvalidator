<?php
namespace shgysk8zer0\DOMValidator;
use \shgysk8zer0\Core\Console as Console;

error_reporting(\E_ALL);

spl_autoload_register('spl_autoload');
set_include_path(dirname(dirname(__DIR__)));

set_error_handler(function(...$args)
{
	header('Content-Type: application/json');
	echo json_encode($args);
	exit();
});

set_exception_handler(function($e) {
	header('Content-Type: application/json');
	echo json_encode([
		'message' => $e->getMessage(),
		'file'    => $e->getFile(),
		'line'    => $e->getLine(),
		'trace'   => $e->getTrace(),
	]);
});


if (empty($_REQUEST)) {
	header('Content-Type: text/html');
	readfile('./forms/test.html');
} else {
	$form = Form::loadFromFile('forms/test.html');
	header('Content-Type: application/json');
	$filtered = $form($_REQUEST);
	Console::log([
		'$_REQUEST' => $_REQUEST,
		'$_FILES' => $_FILES,
	])->sendLogHeader();
	echo json_encode($filtered);
}

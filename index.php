<?php
namespace shgysk8zer0\DOMValidator;

error_reporting(\E_ALL);

spl_autoload_register('spl_autoload');
set_include_path(dirname(dirname(__DIR__)));

$dom = new \DOMDocument();
$dom->loadHTML(file_get_contents('forms/test.html'));

if (empty($_REQUEST['form'])) {
	exit($dom->saveHTML());
} elseif (file_exists("./forms/{$_REQUEST['form']}.html")) {
	header('Content-Type: application/json');
	exit(json_encode(Form::getFiles($_FILES)));
	$validator = new Form($dom->getElementsByTagName('form')->item(0), Form::getUploadedFiles($_REQUEST));
	exit(json_encode(['_files' => $_FILES, '_REQUEST' => $_REQUEST, 'validator' => $validator]));
}

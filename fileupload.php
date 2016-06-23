<?php
namespace shgysk8zer0\DOMValidator;

trait FileUpload
{
	private static $_fkeys = array('error', 'name', 'size', 'tmp_name', 'type');
	final public static function getUploadedFiles()
	{
		return array_filter($_FILES, function($file)
		{
			return !preg_match('/image\/jpeg/', $file['type']);
		});
		return ['post' => $_POST, 'files' => $_FILES];
		$files = array_reduce(
			$_FILES,
			[__CLASS__, '_reduceFiles'],
			array()
		);
		return array_combine(array_keys($_FILES), $files);
	}

	final private static function _mapFileNames($file)
	{

	}

	final private static function _reduceFiles(Array $files, $file)
	{
		$obj = new \stdClass();
		if (static::isUploadedFile($file)) {
			$files[] = true;
		} else {
			$files[] = $file;
		}

		return $files;

		$files[] = $value;
		return $files;
	}

	final public static function isUploadedFile($file)
	{
		// $fkeys = array('error', 'name', 'size', 'tmp_name', 'type');
		return is_array($file) and empty(array_diff(array_keys($file), static::$_fkeys)) and ! is_array($file['error']);
	}

	final public static function isValidFile(Array $file, $accept = null)
	{
		return $file['error'] === 0 and $file['size'] !== 0;
	}

	final private static function _getFileObject($file)
	{
		if (static::isValidFile($file)) {
			return (object)$file;
		}
	}
}

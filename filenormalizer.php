<?php
namespace shgysk8zer0\DOMValidator;
trait FileNormalizer
{
	final public static function getFiles()
	{
		// @see https://php.net/manual/en/features.file-upload.multiple.php#118180
		$files = array();
		foreach ($_FILES as $name => $values) {
			// init for array_merge
			if (!isset($files[$name])) {
				$files[$name] = array();
			}
			if (!is_array($values['error'])) {
				// normal syntax
				$files[$name] = $values;
			} else {
				// html array feature
				foreach ($values as $fileInfoKey => $subArray) {
					$files[$name] = array_replace_recursive($files[$name], self::_fileWalker($subArray, $fileInfoKey));
				}
			}
		}

		return $files;
	}

	final private static function _fileWalker(Array $arr, $fileInfokey)
	{
		$ret = array();
		foreach ($arr as $k => $v) {
			if (is_array($v)) {
				$ret[$k] = \call_user_func("self::". __FUNCTION__, $v, $fileInfokey);
			} else {
				$ret[$k][$fileInfokey] = $v;
			}
		}
		return $ret;
	}
}

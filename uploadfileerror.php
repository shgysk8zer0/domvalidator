<?php
namespace shgysk8zer0\DOMValidator;

final class UploadFileError extends \ErrorException
{
	const OK         = 0;
	const INI_SIZE   = 1;
	const FORM_SIZE  = 2;
	const PARTIAL    = 3;
	const NO_FILE    = 4;
	const NO_TMP_DIR = 6;
	const CANT_WRITE = 7;
	const EXTENSION  = 8;

	private $_formats = array(
		self::OK         => 'No problems were detected with %s.',
		self::INI_SIZE   => 'Size of %s exceeds what is allowed in php.ini.',
		self::FORM_SIZE  => '%s exceeds MAX_FILE_SIZE as specified in the form.',
		self::PARTIAL    => '%s appears to have only been partially uploaded.',
		self::NO_FILE    => 'No file was uploaded.',
		self::NO_TMP_DIR => 'Temporary upload directory is missing. Unable to save %s.',
		self::CANT_WRITE => 'Unable to save %s to disk.',
		self::EXTENSION  => 'A PHP extension stopped upload of %s. It cannot be known which or why.'
	);

	public function __toString()
	{
		if (array_key_exists($this->getCode(), $this->_formats)) {
			return sprintf($this->_formats[$this->getCode()], $this->getMessage());
		} else {
			return "Unknown error occured: '{$this->getMessage()}'";
		}
	}

	public static function checkFile(Array $file)
	{
		if (array_key_exists('tmp_name', $file) and is_uploaded_file($file['tmp_name'])) {
			if ($file['error'] !== self::OK) {
				throw new self($file['name'], $file['error'], \E_WARNING);
			} else {
				return true;
			}
		} else {
			throw new \InvalidArgumentException(sprintf(
				'Arguments passed to %s must be a valid file upload. %s given.'
			), __METHOD__, typeof($file));
		}
	}
}

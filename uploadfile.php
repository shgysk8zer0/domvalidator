<?php
namespace shgysk8zer0\DOMValidator;

final class UploadFile
{
	private $_data = array(
		'name'     => '',
		'size'     => 0,
		'type'     => '',
		'tmp_name' => '',
		'error'    => 0
	);

	private $_path = '';

	const MAGIC_PROPERTY = '_data';

	public function __construct(Array $file)
	{
		if ($this->isUploadedFile($file)) {
			$this->_data = $file;
			$this->_path = $this->tmp_name;
		}
	}

	public function __isset($prop)
	{
		return array_key_exists($prop, $this->{self::MAGIC_PROPERTY});
	}

	public function __get($prop)
	{
		if ($this->__isset($prop)) {
			return $this->{self::MAGIC_PROPERTY}[$prop];
		}
	}

	public function __toString()
	{
		return $this->_path;
	}

	public function moveTo($loc)
	{
		if (move_uploaded_file($this->tmp_name, $loc)) {
			$this->_path = $loc;
			return true;
		} else {
			return false;
		}
	}

	public function getHash($algo = 'md5', $raw = false)
	{
		$algo = strtolower($algo);
		if (in_array($algo, hash_algos())) {
			return hash_file($algo, $this, $raw);
		} else {
			throw new \Exception("$algo is not a supported hash algorithm.");
		}
	}

	public function matchesHash($hash)
	{

	}

	public function matchesPattern($pattern)
	{
		return preg_match($pattern, '/' . preg_quote($this->type, '/') . '/');
	}

	public function isUploadedFile(array $file)
	{
		return array_key_exists('tmp_name', $file) and is_uploaded_file($file['tmp_name']);
	}
}

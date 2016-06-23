<?php

namespace shgysk8zer0\DOMValidator;
/**
 * @todo Check for additional inputs and allow for required additional ones.
 */
final class Form
{
	use InputValidation;

	public $is_valid = true;
	private $_submitted = [];
	private $_form;
	private $_invalid_inputs = array();

	/**
	 * [__construct description]
	 * @param DOMElement $form      [description]
	 * @param Array      $submitted [description]
	 */
	public function __construct(\DOMElement $form, Array $submitted)
	{
		$this->_form = $form;
		$this->_submitted = static::_getInputNames($submitted);
	}

	/**
	 * [__toString description]
	 * @return string [description]
	 */
	public function __toString()
	{
		return json_encode($this->_validateForm());
	}

	/**
	 * [__get description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function __get($name)
	{
		return $this->_submitted[$name];
	}

	/**
	 * [__isset description]
	 * @param  [type]  $name [description]
	 * @return boolean       [description]
	 */
	public function __isset($name)
	{
		return array_key_exists($name, $this->_submitted);
	}

	/**
	 * [__invoke description]
	 * @return [type] [description]
	 */
	public function __invoke()
	{
		return $this->_validateForm();
	}

	/**
	 * Static method to validate a form
	 * @param  DOMElement $form       The form to validate from
	 * @param  Array      $submitted  Array of inputs, such as $_REQUEST
	 * @return bool       Whether or not the form is valid
	 */
	public static function validate(\DOMElement $form, Array $submitted)
	{
		$validator = new self($form, $submitted);
		return $validator();
	}

	/**
	 * [validateFromFile description]
	 * @param  [type] $form      [description]
	 * @param  Array  $submitted [description]
	 * @return [type]            [description]
	 */
	public static function validateFromFile($form, Array $submitted)
	{
		if (file_exists($form)) {
			return static::validateFromHTML(file_get_contents($file));
		} else {
			return false;
		}
	}

	/**
	 * [validateFromHTML description]
	 * @param  [type]  $form       [description]
	 * @param  Array   $submitted  [description]
	 * @param  integer $form_index [description]
	 * @return [type]              [description]
	 */
	public static function validateFromHTML($form, Array $submitted, $form_index = 0)
	{
		$dom = new \DOMDocument();
		$dom->loadHTML($form);
		return static::validate($dom->getElementsByTagName('form')[$form_index], $submitted);
	}

	/**
	 * [_getInputNames description]
	 * @param  Array  $inputs [description]
	 * @return [type]         [description]
	 */
	private static function _getInputNames(Array $inputs)
	{
		$inputs = urldecode(http_build_query($inputs));
		$inputs = explode('&', $inputs);
		return array_reduce($inputs, function(Array $inputs, $input) {
			list($name, $value) = explode('=', $input);
			$inputs[$name] = $value;
			return $inputs;
		}, []);
	}

	/**
	 * [_validate description]
	 * @return [type] [description]
	 */
	private function _validateForm()
	{
		$inputs = $this->_form->getElementsByTagName('input');
		$selects = $this->_form->getElementsByTagName('select');
		$textareas = $this->_form->getElementsByTagName('textarea');

		foreach($inputs as $input) {
			if ($this->isInvalidInput($input, $this->submitted[$input->getAttribute('name')])) {
				array_push($this->_invalid_inputs, $input->getAttribute('name'));
			}
		}

		foreach ($selects as $select) {
			if ($this->isInvalidSelect($select, $this->submitted[$select->getAttribute('name')])) {
				array_push($this->_invalid_inputs, $select->getAttribute('name'));
			}
		}

		foreach ($textareas as $textarea) {
			if ($this->isInvaidTextarea($textarea, $this->submitted[$textarea->getAttribute('name')])) {
				array_push($this->_invalid_inputs, $textarea->getAttribute('name'));
			}
		}

		$this->is_valid = empty($this->_invalid_inputs);
		return $this->_invalid_inputs;
	}

	public static function getFiles()
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

	private static function _fileWalker(Array $arr, $fileInfokey)
	{
		$ret = array();
		foreach ($arr as $k => $v) {
			if (is_array($v)) {
				$ret[$k] = \call_user_func(__METHOD__, $v, $fileInfokey);
			} else {
				$ret[$k][$fileInfokey] = $v;
			}
		}
		return $ret;
	}
}

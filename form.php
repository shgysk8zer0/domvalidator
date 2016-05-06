<?php

// namespace shgysk8zer0\DOMValidator;
/**
 * @todo Check for additional inputs and allow for required additional ones.
 */
final class Form
{
	use InputValidation;
	public $is_valid = true;
	public $submitted = [];
	public $inputs = [];
	private $_selectors = array('input', 'select', 'textarea');
	private $_invalid_inputs = array();

	/**
	 * [__construct description]
	 * @param DOMElement $form      [description]
	 * @param Array      $submitted [description]
	 */
	public function __construct(\DOMElement $form, Array $submitted)
	{
		$xpath = new \DOMXPath($form->ownerDocument);
		$this->inputs = $xpath->query('input', $form);
		$this->submitted = $this->_getInputNames($submitted);
	}

	/**
	 * [__toString description]
	 * @return string [description]
	 */
	public function __toString()
	{
		return json_encode($this->_validate());
	}

	/**
	 * [__invoke description]
	 * @return [type] [description]
	 */
	public function __invoke()
	{
		return $this->_validate();
	}

	/**
	 * [getUploadedFiles description]
	 * @param  Array  $inputs [description]
	 * @return [type]         [description]
	 */
	public static function getUploadedFiles(Array $inputs)
	{
		$uploaded = array_map(function($file)
		{
			return $file['name'];
		}, $_FILES);
		return array_merge($inputs, $uploaded);
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
		$validator();
		return $validator->is_valid;
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
	private function _getInputNames(Array $inputs)
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
	private function _validate()
	{
		foreach($this->inputs as $input) {
			if ($this->isInvalidInput($input, $this->submitted[$input->getAttribute('name')])) {
				array_push($this->_invalid_inputs, $input->getAttribute('name'));
			}
		}
		$this->is_valid = empty($this->_invalid_inputs);
		return $this->_invalid_inputs;
	}
}

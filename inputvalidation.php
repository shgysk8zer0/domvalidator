<?php
// namespace shgysk8zer0\DOMValidator;
trait InputValidation
{
	/**
	 * [_validateByType description]
	 * @param  DOMElement $input [description]
	 * @param  [type]     $value [description]
	 * @return [type]            [description]
	 * @see https://secure.php.net/manual/en/function.filter-var.php
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input
	 * @todo Handle files
	 * @todo Check step for numeric inputs
	 * @todo Handle `multiple`
	 */
	final public function validateInput(\DOMElement $input, $value)
	{
		$type = $input->hasAttribute('type') ? $input->getAttribute('type') : 'text';
		$opts = ['options' => []];

		switch (strtolower($type)) {
			case 'text':
			case 'password':
			case 'search':
			case 'hidden':
				return $this->validLength($input, $value) and $this->matchesPattern($input, $value);
				break;

			case 'button':
			case 'submit':
			case 'reset':
				return true;
				break;

			case 'number':
			case 'range':
				if ($input->hasAttribute('min') and is_numeric($input->getAttribute('min'))) {
					$opts['options']['min_range'] = $input->getAttribute('min');
				}
				if ($input->hasAttribute('max') and is_numeric($input->getAttribute('max'))) {
					$opts['options']['max_range'] = $input->getAttribute('max');
				}
				$step = $input->hasAttribute('step') and is_numeric($input->getAttribute('step')) ? $input->getAttribute('step') : 1;
				return filter_var($value, FILTER_VALIDATE_INT, $opts);
				break;

			case 'url':
				return filter_var($value, FILTER_VALIDATE_URL);
				break;

			case 'email':
				return filter_var($value, FILTER_VALIDATE_EMAIL);
				break;
			case 'tel':
				return true;
				break;

			case 'file':
				return true;
				break;

			case 'datetime':
			case 'datetime-local':
				return true;
				break;

			case 'time':
				return true;
				break;

			case 'week':
				return true;
				break;

			case 'color':
				return true;
				break;

			default:
				return true;
				break;
		}
	}


	/**
	 * [isValidInput description]
	 * @param  DOMElement $input [description]
	 * @param  [type]     $value [description]
	 * @return boolean           [description]
	 */
	final public function isValidInput(\DOMElement $input, $value)
	{
		if ($input->hasAttribute('required')) {
			return $this->validateInput($input, $value);
		} else {
			return $value === '' or $this->validateInput($input, $value);
		}
	}

	/**
	 * [isInvalidInput description]
	 * @param  DOMElement $input [description]
	 * @param  [type]     $value [description]
	 * @return boolean           [description]
	 */
	final public function isInvalidInput(\DOMElement $input, $value)
	{
		return ! $this->isValidInput($input, $value);
	}

	/**
	 * [validLength description]
	 * @param  DOMElement $input [description]
	 * @param  [type]     $value [description]
	 * @return [type]            [description]
	 */
	final public function validLength(\DOMElement $input, $value)
	{
		if ($input->hasAttribute('minlength') and strlen($value) < $input->getAttribute('minlength')) {
			return false;
		}
		if ($input->hasAttribute('maxlength') and strlen($value) > $input->getAttribute('maxlength')) {
			return false;
		}
		return true;
	}

	/**
	 * [_testPattern description]
	 * @param  DOMElement $input [description]
	 * @param  [type]     $value [description]
	 * @return [type]            [description]
	 */
	final public function matchesPattern(\DOMElement $input, $value)
	{
		if ($input->hasAttribute('pattern')) {
			return preg_match("/{$input->getAttribute('pattern')}/", $value);
		} else {
			return true;
		}
	}

	/**
	 * [_isValidUpload description]
	 * @param  DOMElement $input  [description]
	 * @param  [type]     $upload [description]
	 * @return boolean            [description]
	 * @see https://secure.php.net/manual/en/features.file-upload.php
	 */
	final public function isValidUpload(\DOMElement $input, $upload)
	{
		if ($input->hasAttribute('required') and ! array_key_exists($uplaod, $_FILES)) {
			return false;
		} elseif ($input->hasAttribute('accept')) {
			return preg_match('/^' . preg_quote($input->getAttribute('accept'), '/') . '$/', $_FILES[$upload]['type']);
		} else {
			return true;
		}
	}

	/**
	 * [missingRequiredInput description]
	 * @param  DOMElement $input [description]
	 * @param  [type]     $value [description]
	 * @return [type]            [description]
	 */
	final public function missingRequiredInput(\DOMElement $input, $value)
	{
		return $input->hasAttribute('required') and $value === '';
	}
}

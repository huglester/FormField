<?php namespace Way\Form;

use Form, Config;

class FormField {

	/**
	 * Instance
	 *
	 * @var Way\Form\FormField
	 */
	protected static $instance;

	protected $messages;

	function __construct()
	{
		$bag = \Session::get('errors');
		if ($bag)
		{
			$this->messages = $bag;
		}
	}

	/**
	 * Make the form field
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return mixed
	 */
	public function make($name, array $args)
	{
		$wrapper       = $this->createWrapper();
		$field         = $this->createField($name, $args);
		$error_class   = ($this->getError($name)) ? Config::get('form::wrapperErrorClass') : null;

		$search = ['{{FIELD}}', '{{WRAPPER_ERROR_CLASS}}', '{{INLINE_ERRORS}}'];

		$errors_inline  = null;
		if ($error_class and Config::get('form::inlineErrors'))
		{
			$errors_inline = sprintf(Config::get('form::inlineErrorsTemplate'), $this->getError($name));
		}

		$replace = [$field, $error_class, $errors_inline];

		return str_replace($search, $replace, $wrapper);
	}

	/**
	 * Prepare the wrapping container for
	 * each field.
	 */
	protected function createWrapper()
	{
		$wrapper      = Config::get('form::wrapper');
		$wrapperClass = Config::get('form::wrapperClass');

		return '<'.$wrapper.' class="'.$wrapperClass.' {{WRAPPER_ERROR_CLASS}}">
		{{FIELD}}
		{{INLINE_ERRORS}}
		</'.$wrapper.'>';
	}

	/**
	 * Create the form field
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return string
	 */
	protected function createField($name, $args)
	{
		// If the user specifies an input type, we'll just use that.
		// Otherwise, we'll take a best guess approach.
		$type = array_get($args, 'type') ?: $this->guessInputType($name);

		// We'll default to Bootstrap-friendly input class names
		$args = array_merge(['class' => Config::get('form::inputClass')], $args);

		$field = $this->createLabel($args, $name);

		unset($args['label']);

		return $field . $this->createInput($type, $args, $name);
	}

	/**
	 * Handle of creation of the label
	 *
	 * @param array  $args
	 * @param string $name
	 *
	 * @return string
	 */
	protected function createLabel($args, $name)
	{
		$label = array_get($args, 'label');


		// If no label was provided, let's check out conrigs, to see if there
		// are some defaults
		is_null($label) and $label = $this->checkLabelConfig($name);

		// We passed the label, suffix it!
		if ($label)
		{
			$label .= ':';
		}

		// If no label was provided, let's do our best to construct
		// a label from the method name.
		is_null($label) and $label = $this->prettifyFieldName($name).':';

		return $label ? Form::label($name, $label, array('class' => Config::get('form::labelClass'))) : '';
	}

	/**
	 * Manage creation of input
	 *
	 * @param string $type
	 * @param array  $args
	 * @param string $name
	 * @param array  $options
	 *
	 * @return string
	 */
	protected function createInput($type, $args, $name)
	{
		if (isset($args['value']) and $type != 'password')
		{
			$value = $args['value'];
			unset($args['value']);
		} else
		{
			$value = null;
		}

		if ($type === 'select')
		{
			$options = [];
			if (isset($args['options']))
			{
				$options = $args['options'];
				unset($args['options']);
			}

			return Form::select($name, $options, $value, $args);
		}
		elseif ($type === 'boolean')
		{
			$options = [1 => 'Yes', 0 => 'No'];
			if (isset($args['options']))
			{
				$options = $args['options'];
				unset($args['options']);
			}

			return Form::select($name, $options, $value, $args);
		}


		return $type == 'password'
			? Form::password($name, $args)
			: Form::$type($name, $value, $args);
	}

	/**
	 * Provide a best guess for what the
	 * input type should be.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	protected function guessInputType($name)
	{
		return array_get(Config::get('form::commonInputsLookup'), $name) ?: 'text';
	}

	/**
	 * Clean up the field name for the label
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	protected function prettifyFieldName($name)
	{
		return ucwords(preg_replace('/(?<=\w)(?=[A-Z])/', " $1", $name));
	}

	/**
	 * Check if config has default label for a field
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	protected function checkLabelConfig($name)
	{
		return array_get(Config::get('form::defaultLabels'), $name) ?: null;
	}

	/**
	 * Handle dynamic method calls
	 *
	 * @param string $name
	 * @param array  $args
	 */
	public static function __callStatic($name, $args)
	{
		$args = empty($args) ? [] : $args[0];

		$instance = static::$instance;
		if ( ! $instance) $instance = static::$instance = new static;

		return $instance->make($name, $args);
	}

	private function getError($name)
	{
		if ( ! empty($this->messages))
		{
			return $this->messages->first($name);
		}

		return null;
	}

}

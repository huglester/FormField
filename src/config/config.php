<?php

return [

    /*
     * What should each form element be
     * wrapped within?
    */
    'wrapper' => 'div',

    /*
     * What class should the wrapper
     * element receive?
    */
    'wrapperClass' => 'form-group',

    /**
     * Should form inputs receive a class name?
     */
    'inputClass' => 'form-control',

	/*
	 * What class should the wrapper
	 * element receive if error exist?
	*/
	'wrapperErrorClass'  => 'has-error',

	/*
	 * Inline errors?
	 * */
	'inlineErrors'       => true,

	/*
	 * Inline errors template?
	 * */
	'inlineErrorsTemplate' => '<span class="help-block">%s</span>',

    /**
     * Frequent input names can map
     * to their respective input types.
     *
     * This way, you may do FormField::description()
     * and it'll know to automatically set it as a textarea.
     * Otherwise, do FormField::thing(['type' => 'textarea'])
     *
     */
    'commonInputsLookup'  => [
        'email' => 'email',
        'emailAddress' => 'email',

        'description' => 'textarea',
        'bio' => 'textarea',
        'body' => 'textarea',

        'password' => 'password',
        'password_confirmation' => 'password'
    ]
];

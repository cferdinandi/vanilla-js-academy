<?php

	/**
	 * Get user basic auth credentials
	 * @return Array The user credentials
	 */
	function get_basic_auth_credentials () {

		$auth_header = $_SERVER['HTTP_AUTHORIZATION'];

		// If there's no auth header, fail
		if (empty($auth_header)) return false;

		// Get credentials
		$auth = base64_decode(trim(substr($auth_header, 5)));
		$credentials = explode(':', trim($auth));

		// If there are no credentials, fail
		if (empty($credentials[0]) || empty($credentials[1])) return false;

		// Otherwise, return decoded credentials
		return array(
			'username' => strtolower($credentials[0]),
			'password' => $credentials[1]
		);

	}

	/**
	 * Validate user credentials
	 * @param  Array   $credentials The user credentials
	 * @return Boolean              If true, credentials are valid
	 */
	function are_credentials_valid ($credentials) {
		return $credentials['username'] === 'jack' && $credentials['password'] === 'treasure';
	}

	/**
	 * Send an API response
	 * @param  *       $response The API response
	 * @param  integer $code     The response code
	 * @param  boolean $encode   If true, encode response
	 */
	function send_response ($response, $code = 200, $encode = false) {
		http_response_code($code);
		die(json_encode($response));
	}

	// Get user credentials
	$credentials = get_basic_auth_credentials();

	// if there are no credentials, or they're not valid, throw an error
	if (!are_credentials_valid($credentials)) {
		send_response('Please provide a valid username and password.', 401);
	}

	// Return the token
	send_response(array(
		'token' => bin2hex(random_bytes(24))
	));
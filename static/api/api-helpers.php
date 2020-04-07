<?php

	require_once('Requests/library/Requests.php');
	Requests::register_autoloader();

	function extract_data () {
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		return $_POST;
	}

	// Check if email matches a customer
	function is_customer ($email) {

		// Get API crednetials
		$key = getenv('EDD_KEY');
		$username = getenv('EDD_USERNAME');
		if (empty($key) || empty($username)) return false;

		// Get a list of customer purchases
		$url = 'https://gomakethings.com/checkout/wp-json/gmt-edd/v1/users/' . $email;
		$headers = array('Authorization' => 'Basic ' . base64_encode($username . ':' . $key));
		$request = Requests::get($url, $headers);
		$response = json_decode($request->body, true);

		// Check if user has made a purchase
		return !empty($response['purchases']);

	}

	// Get user session token
	function get_token_credentials () {

		$auth_header = $_SERVER['HTTP_AUTHORIZATION'];

		// If there's no auth header, fail
		if (empty($auth_header)) return false;

		// Get auth token
		$auth = explode('Bearer', $auth_header);
		$token = trim($auth[1]);

		// If there's no token, fail
		if (empty($token)) return false;

		// Check token against list
		$pairs = file_exists('_/oauth-pairs.json') ? json_decode(file_get_contents('_/oauth-pairs.json')) : new stdClass();
		if (!in_array($token, $pairs)) return false;

		// Otherwise, token passes
		return $token;

	}

	// Get user basic auth credentials
	function get_basic_auth_credentials () {

		$auth_header = $_SERVER['HTTP_AUTHORIZATION'];

		// If there's no auth header, fail
		if (empty($auth_header)) return false;

		// Get credentials
		$auth = trim(substr($auth_header, 5));
		$credentials = explode(':', trim($auth));

		// If there are no credentials, fail
		if (empty($credentials[0]) || empty($credentials[1])) return false;

		// Otherwise, return decoded credentials
		return array(
			'email' => base64_decode($credentials[0]),
			'password' => base64_decode($credentials[1])
		);

	}

	// Check if an unauthorized request matches a valid user
	function get_user ($user) {

		// Hash the $user
		$hash = sha1($user);

		// Check hash against list
		$pairs = file_exists('_/oauth-pairs.json') ? json_decode(file_get_contents('_/oauth-pairs.json')) : new stdClass();
		if (!property_exists($pairs, $hash)) return false;

		// Otherwise, return the $hash
		return $pairs->{$hash};

	}

	// Get the API method
	function get_method () {
		if (in_array($_SERVER['REQUEST_METHOD'], array('GET', 'POST', 'PUT', 'DELETE'))) {
			return $_SERVER['REQUEST_METHOD'];
		}
		http_response_code(400);
		die('Bad request');
	}

	// Get file
	function get_file ($hash, $filename, $fallback, $is_file) {

		// File path
		$path = '_/' . $hash . '_' . $filename . '.json';

		// If file exists, return it
		if ($hash && file_exists($path)) {
			return json_decode(file_get_contents($path));
		}

		// If there's a fallback, use that
		if (!empty($fallback)) {
			if (!empty($is_file)) {
				if (file_exists($fallback)) {
					return json_decode(file_get_contents($fallback));
				}
			} else {
				return $fallback;
			}
		}

		// Otherwise, return false
		return false;

	}

	// Create/update a file
	function set_file ($hash, $filename, $content) {
		file_put_contents('_/' . $hash . '_'. $filename . '.json', json_encode($content));
	}
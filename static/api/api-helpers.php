<?php

	include_once('api-list.php');

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

	function get_oauth_pairs () {
		return file_exists('_/oauth-pairs.json') ? json_decode(file_get_contents('_/oauth-pairs.json')) : new stdClass();
	}

	function get_oauth_sessions () {
		return file_exists('_/oauth-sessions.json') ? json_decode(file_get_contents('_/oauth-sessions.json')) : new stdClass();
	}

	function set_oauth_session ($path) {
		$duration = empty($_GET['exp']) ? 60 * 15 : $_GET['exp'];
		$exp = time() + $duration;
		$sessions = get_oauth_sessions();
		$sessions->{$path} = $exp;
		file_put_contents('_/oauth-sessions.json', json_encode($sessions));
		return $exp;
	}

	function remove_oauth_session ($path) {
		$sessions = get_oauth_sessions();
		if (!property_exists($session, $path)) return;
		unset($sessions->{$path});
	}

	function is_session_valid ($path) {
		$session = get_oauth_sessions();
		return property_exists($session, $path) && time() < $session->{$path};
	}

	function authenticate_user ($path) {

		// Make sure there's a valid path
		if (empty($path)) {
			http_response_code(403);
			die('You shall not pass!');
		}

		// Make sure session is still valid
		$is_valid = is_session_valid($path);
		if (empty($is_valid)) {
			http_response_code(440);
			die('Session expired');
		}

	}

	// Get user session token
	function get_path_from_token () {

		$auth_header = $_SERVER['HTTP_AUTHORIZATION'];

		// If there's no auth header, fail
		if (empty($auth_header)) return false;

		// Get auth token
		$auth = explode('Bearer', $auth_header);
		$token = trim($auth[1]);

		// If there's no token, fail
		if (empty($token)) return false;

		// Check token against list
		$pairs = get_oauth_pairs();
		if (!property_exists($pairs, $token)) return false;

		// Otherwise, return token value
		return $pairs->{$token};

	}

	// Check if an unauthorized request matches a valid user
	function get_path_from_user ($user) {

		// Hash the $user
		$hash = sha1($user);

		// Check hash against list
		$pairs = get_oauth_pairs();
		if (!property_exists($pairs, $hash)) return false;

		// Otherwise, return the $hash
		return $pairs->{$hash};

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
			'email' => strtolower(base64_decode($credentials[0])),
			'password' => base64_decode($credentials[1])
		);

	}

	// Get file path from user credentials
	function get_path () {
		$basic = get_basic_auth_credentials();
		$path = !empty($basic) && array_key_exists('email', $basic) ? get_path_from_user($basic['email']) : get_path_from_token();
		return $path;
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
	function get_file ($hash, $filename, $fallback, $is_file = false) {

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

	function find_by_key_value ($key, $value, $array = array()) {
		foreach ($array as $index => $val) {
			if (array_key_exists($key, $val) && $val[$key] === $value) {
				return $index;
			}
		}
		return null;
	}
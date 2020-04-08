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
		$pairs = file_exists('_/oauth-pairs.json') ? json_decode(file_get_contents('_/oauth-pairs.json')) : new stdClass();
		if (!property_exists($pairs, $token)) return false;

		// Otherwise, return token value
		return $pairs->{$token};

	}

	// Check if an unauthorized request matches a valid user
	function get_path_from_user ($user) {

		// Hash the $user
		$hash = sha1($user);

		// Check hash against list
		$pairs = file_exists('_/oauth-pairs.json') ? json_decode(file_get_contents('_/oauth-pairs.json')) : new stdClass();
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
			'email' => base64_decode($credentials[0]),
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

	// Return an array of API info
	function get_api_help ($user) {
		$url = 'https://vanillajsacademy.com/api/';
		return array(
			'dogs' => array(
				'description' => 'A list of adoptable dogs.',
				'endpoint' => $url . 'dogs.json',
				'publicEndpoint' => $url . 'dogs.json',
				'methods' => array('GET'),
				'authentication' => null,
			),
			'echo' => array(
				'description' => 'A test endpoint that returns any data you send. Does not store data.',
				'endpoint' => $url . 'echo.php',
				'publicEndpoint' => null,
				'methods' => array('POST', 'PUT'),
				'authentication' => 'Bearer',
			),
			'oauth' => array(
				'description' => 'Get an OAuth Bearer token for use with API requests.',
				'endpoint' => $url . 'outh.php',
				'publicEndpoint' => null,
				'methods' => array('GET'),
				'authentication' => 'Basic',
			),
			'pirates' => array(
				'description' => 'Get a list of articles for the Scuttlebutt pirate publication.',
				'endpoint' => $url . 'pirates.json',
				'publicEndpoint' => $url . 'pirates.json',
				'methods' => array('GET'),
				'authentication' => null,
			),
			'scavengerHuntSingle' => array(
				'description' => 'A single scavenger hunt list.',
				'endpoint' => $url . 'scavenger-hunt.json',
				'publicEndpoint' => $url . 'scavenger-hunt.json',
				'methods' => array('GET'),
				'authentication' => null,
			),
			'scavengerHunt' => array(
				'description' => 'Scavenger hunt lists.',
				'endpoint' => $url . 'scavenger-hunt.php',
				'publicEndpoint' => $url . 'scavenger-hunt.php' . '?public=' . $user,
				'methods' => array('GET', 'POST', 'PUT', 'DELETE'),
				'authentication' => 'Bearer',
			),
		);
	}
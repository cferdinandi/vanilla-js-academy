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

	function is_authorized () {

		$headers = apache_request_headers();

		// If there's no auth header, fail
		if (empty($headers) || empty($headers['Authorization'])) return false;

		// Get auth token
		$auth = explode('Bearer', $headers['Authorization']);
		$token = trim($auth[1]);

		// If there's no token, fail
		if (empty($token)) return false;

		// Check token against list
		$pairs = json_decode(file_get_contents('_/oauth-pairs.json'));
		if (!in_array($token, $pairs)) return false;

		// Otherwise, token passes
		return $token;

	}

	// Check if an unauthorized request matches a valid user
	function get_user ($user) {

		// Hash the $user
		$hash = md5($user);

		// CHeck hash against list
		$pairs = json_decode(file_get_contents('_/oauth-pairs.json'));
		if (!in_array($hash, $pairs)) return false;

		// Otherwise, return the $hash
		return $hash;

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
	function get_file ($hash, $filename, $fallback) {

		// File path
		$path = '_/' . $hash . '_' . $filename . '.json';

		// If file exists, return it
		if (file_exists($path)) {
			return json_decode(file_get_contents($path));
		}

		// If there's a fallback, use that
		if ($fallback) {
			return $fallback;
		}

		// Otherwise, return false
		return false;

	}

	// Create/update a file
	function set_file ($hash, $filename, $content) {
		file_put_contents('_/' . $hash . '_'. $filename . '.json', json_encode($content));
	}
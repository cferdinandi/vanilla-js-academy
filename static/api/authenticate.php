<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method
	$method = get_method();

	// GET request
	// Getting a new token
	if ($method === 'GET') {

		// Get credentials
		$credentials = get_basic_auth_credentials();

		// Make sure a username and password are provided
		if (empty($credentials)) {
			http_response_code(500);
			die('Please provide a valid email address and password.');
		}

		// Check if token already exists
		$pairs = get_oauth_pairs();
		$hash = sha1($credentials['email']);
		$path = md5($credentials['email']);

		// If not, check if allowed to have one
		if (!property_exists($pairs, $hash)) {

			// If the email matches a customer, return a token
			// Otherwise, throw an error
			if (is_customer($credentials['email'])) {
				$pairs->{$hash} = $path;
				file_put_contents('_/oauth-pairs.json', json_encode($pairs));
			} else {
				http_response_code(403);
				die('You shall not pass!');
			}

		}

		// Set session
		$exp = set_oauth_session($path);

		// Return the token
		http_response_code(200);
		die(json_encode(array(
			'access_token' => $hash,
			'public_path' => '?public=' . $path,
			'exp' => $exp,
			'token_type' => 'Bearer'
		)));

	}

	// Authenticate user
	$path = get_path_from_token();
	authenticate_user($path);

	// PUT/POST request
	// Extending an existing session token
	if ($method === 'POST' || $method === 'PUT') {

		// Update the session
		$exp = set_oauth_session($path);

		// Return the updated token
		http_response_code(200);
		die(json_encode(array(
			'exp' => $exp,
		)));

	}

	// DELETE request
	// Revoke an existing session token
	if ($method === 'DELETE') {

		// Remove the session
		remove_oauth_session($path);

		// Return the updated token
		http_response_code(200);
		die(json_encode(array(
			'exp' => 0,
		)));

	}

	// All other requests
	if ($method !== 'GET') {
		http_response_code(405);
		die('Method not allowed');
	}
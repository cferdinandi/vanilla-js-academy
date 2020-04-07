<?php

	// Include required files
	include_once('api-helpers.php');

	// Get data
	extract_data();

	// Get credentials
	$credentials = get_basic_auth_credentials();

	// Make sure a username and password are provided
	if (empty($credentials)) {
		http_response_code(500);
		die('Please provide a valid email address and password.');
	}

	// Check if token already exists
	$pairs = file_exists('_/oauth-pairs.json') ? json_decode(file_get_contents('_/oauth-pairs.json')) : array();
	$hash = md5($credentials['email']);

	// If not, check if allowed to have one
	if (!in_array($hash, $pairs)) {

		// If the email matches a customer, return a token
		// Otherwise, throw an error
		if (is_customer($credentials['email'])) {
			$pairs[] = $hash;
			file_put_contents('_/oauth-pairs.json', json_encode($pairs));
		} else {
			http_response_code(403);
			die('You shall not pass!');
		}

	}

	// Return the token
	http_response_code(200);
	die(json_encode(array(
		'access_token' => $hash,
		'expires' => 1000 * 60 * 60,
		'token_type' => 'Bearer'
	)));
<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method
	$method = get_method();

	// Authenticate user
	$basic = get_basic_auth_credentials();
	$user = !empty($basic) && array_key_exists('email', $basic) ? get_user($basic['email']) : get_token_credentials();
	if (empty($user)) {
		http_response_code(403);
		die('You shall not pass!');
	}

	// GET Request
	if ($method === 'GET') {
		http_response_code(200);
		die(json_encode(get_api_help($user)));
	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
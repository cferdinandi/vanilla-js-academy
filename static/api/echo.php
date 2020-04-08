<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method
	$method = get_method();

	// Authenticate user
	$token = get_token_credentials();
	if (empty($token)) {
		http_response_code(403);
		die('You shall not pass!');
	}

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {
		extract_data();
		http_response_code(200);
		die(json_encode($_POST));
	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
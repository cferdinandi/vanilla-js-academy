<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method
	$method = get_method();

	// Authenticate user
	$path = get_path();
	if (empty($path)) {
		http_response_code(403);
		die('You shall not pass!');
	}

	// GET Request
	if ($method === 'GET') {
		http_response_code(200);
		die(json_encode(get_api_help($path)));
	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
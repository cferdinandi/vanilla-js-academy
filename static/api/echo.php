<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method
	$method = get_method();

	// Authenticate user
	$path = get_path_from_token();
	authenticate_user($path);

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {
		extract_data();
		http_response_code(200);
		die(json_encode($_POST));
	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
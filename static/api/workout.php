<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method
	$method = get_method();

	// Authenticate user
	$path = get_path_from_token();
	authenticate_user($path);

	// GET Request
	if ($method === 'GET') {

		// Get the file
		$file = get_file($path, 'workout', new stdClass());

		// Return the file
		http_response_code(200);
		die(json_encode($file));

	}

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {
		extract_data();
		set_file($path, 'workout', $_POST);
		http_response_code(200);
		die(json_encode($_POST));
	}

	// DELETE Request
	if ($method === 'DELETE') {
		set_file($path, 'workout', '{}');
		http_response_code(200);
		die('{}');
	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
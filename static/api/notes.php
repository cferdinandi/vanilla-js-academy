<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method
	$method = get_method();

	// Get token
	$path = get_path_from_token();

	// Force authorization
	if (empty($path)) {
		http_response_code(403);
		die('You shall not pass!');
	}

	// GET Request
	if ($method === 'GET') {

		// Get the file
		$file = get_file($path, 'notes', 'notes.json', true);

		// Return the file
		http_response_code(200);
		die(json_encode($file));

	}

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {
		extract_data();
		set_file($path, 'notes', $_POST);
		http_response_code(200);
		die(json_encode($_POST));
	}

	// DELETE Request
	if ($method === 'DELETE') {
		set_file($path, 'notes', '{}');
		http_response_code(200);
		die('{}');
	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
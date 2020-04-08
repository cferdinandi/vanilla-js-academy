<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method
	$method = get_method();

	// Get token
	$path = get_path_from_token();

	// GET Request
	if ($method === 'GET') {

		// If there's no matching user, bail
		if (empty($path) && empty($_GET['public'])) {
			http_response_code(403);
			die('You shall not pass!');
		}

		// Get the file
		$file = get_file(empty($path) ? $_GET['public'] : $path, 'scavenger-hunt', 'scavenger-hunt-items.json', true);

		// Return the file
		http_response_code(200);
		die(json_encode($file));

	}

	// For any other type of request, force authorization
	if (empty($path)) {
		http_response_code(403);
		die('You shall not pass!');
	}

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {
		extract_data();
		set_file($path, 'scavenger-hunt', $_POST);
		http_response_code(200);
		die(json_encode($_POST));
	}

	// DELETE Request
	if ($method === 'DELETE') {
		set_file($path, 'scavenger-hunt', '{}');
		http_response_code(200);
		die('{}');
	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
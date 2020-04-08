<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method
	$method = get_method();

	// Get token
	$token = get_token_credentials();

	// GET Request
	if ($method === 'GET') {

		// If there's no matching user, bail
		if (empty($token) && empty($_GET['public'])) {
			http_response_code(403);
			die('You shall not pass!');
		}

		// Get the file
		$file = get_file(empty($token) ? $_GET['public'] : $token, 'scavenger-hunt', 'scavenger-hunt-items.json', true);

		// Return the file
		http_response_code(200);
		die(json_encode($file));

	}

	// For any other type of request, force authorization
	if (empty($token)) {
		http_response_code(403);
		die('You shall not pass!');
	}

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {
		extract_data();
		set_file($token, 'scavenger-hunt', $_POST);
		http_response_code(200);
		die(json_encode($_POST));
	}

	// DELETE Request
	if ($method === 'DELETE') {
		set_file($token, 'scavenger-hunt', '{}');
		http_response_code(200);
		die('{}');
	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
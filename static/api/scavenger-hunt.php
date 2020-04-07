<?php

	// Include required files
	include_once('api-helpers.php');

	// Get data
	extract_data();

	// Get the API method
	$method = get_method();

	// GET Request
	if ($method === 'GET') {

		// Get the hased user token
		$user = get_user($_GET['user']);

		// If not a valid user, bail
		if (empty($user)) {
			http_response_code(403);
			die('You shall not pass!');
		}

		// Get the file
		$file = get_file($user, 'scavenger-hunt', 'scavenger-hunt-items.json', true);

		// Return the file
		http_response_code(200);
		die(json_encode($file));

	}

	// For any other type of request, force authorization
	$token = is_authorized();
	if (!$token) {
		http_response_code(403);
		die('You shall not pass!');
	}

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {
		set_file($token, 'scavenger-hunt', $_POST);
		http_response_code(200);
		die(json_encode($_POST));
	}

	// DELETE Request
	if ($method === 'DELETE') {
		set_file($token, 'scavenger-hunt', '{}');
		http_response_code(200);
		die(json_encode(new stdClass()));
	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
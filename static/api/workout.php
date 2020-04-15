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

	extract_data();

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {

		// If no ID was provided, throw an error
		if (empty($_POST['id'])) {
			http_response_code(400);
			die('Please provide a unique ID for this workout');
		}

		// Get the file
		$file = get_file($path, 'workout', new stdClass());

		// Add or update the item
		$file->{$_POST['id']} = $_POST;

		// Save to database
		set_file($path, 'workout', $file);

		// Return data
		http_response_code(200);
		die(json_encode($_POST));

	}

	// DELETE Request
	if ($method === 'DELETE') {

		// If no ID was provided, reset entire file
		if (empty($_POST['id'])) {
			set_file($path, 'workout', new stdClass());
			http_response_code(200);
			die('{workouts: []}');
		}

		// Get the file
		$file = get_file($path, 'workout', new stdClass());

		// Delete the item
		unset($file->{$_POST['id']});

		// Save to database
		set_file($path, 'workout', $file);

		// Return data
		http_response_code(200);
		die(json_encode($file));

	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
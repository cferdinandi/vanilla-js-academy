<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method & workout ID
	$method = get_method();
	$id = $_GET['id'];

	// Authenticate user
	$path = get_path_from_token();
	authenticate_user($path);
	$filepath = $path . (empty($_GET['weight']) ? '' : '_weight');

	// GET Request
	if ($method === 'GET') {

		// Get the file
		$file = get_file($filepath, 'workout', new stdClass());

		// Return the file
		http_response_code(200);
		die(json_encode($file));

	}

	extract_data();

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {

		// If no ID was provided, throw an error
		if (empty($id)) {
			http_response_code(400);
			die('Please provide a unique ID for this workout');
		}

		// Get the file
		$file = get_file($filepath, 'workout', new stdClass());

		// Add or update the item
		$file->{$id} = $_POST;

		// Save to database
		set_file($filepath, 'workout', $file);

		// Return data
		http_response_code(200);
		die(json_encode($_POST));

	}

	// DELETE Request
	if ($method === 'DELETE') {

		// If no ID was provided, reset entire file
		if (empty($id)) {
			set_file($filepath, 'workout', new stdClass());
			http_response_code(200);
			die(json_encode(new stdClass()));
		}

		// Get the file
		$file = get_file($filepath, 'workout', new stdClass());

		// Delete the item
		unset($file->{$id});

		// Save to database
		set_file($filepath, 'workout', $file);

		// Return data
		http_response_code(200);
		die(json_encode($file));

	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
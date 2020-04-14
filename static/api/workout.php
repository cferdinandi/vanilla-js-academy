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
		$file = get_file($path, 'workout', array('workouts' => array()));

		// Return the file
		http_response_code(200);
		die(json_encode($file));

	}

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {

		extract_data();

		// If no ID was provided, throw an error
		if (empty($_POST['id'])) {
			http_response_code(400);
			die('Please provide a unique ID for this workout');
		}

		// Get the file
		$file = get_file($path, 'workout', array('workouts' => array()));

		// Check if item already exists
		$existing = array_search($_POST['id'], array_column($file->{'workouts'}, 'id'));

		// If the item doesn't exist, create it
		// Otherwise, replace it
		if ($existing === false) {
			$file->{'workouts'}[] = $_POST;
		} else {
			$file->{'workouts'}[$existing] = $_POST;
		}

		// Save to database
		set_file($path, 'workout', $file);

		// Return data
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
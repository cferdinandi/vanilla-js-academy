<?php

	// Include required files
	include_once('api-helpers.php');

	// Get the API method
	$method = get_method();
	$id = $_GET['id'];

	// Authenticate user
	$path = get_path_from_token();
	authenticate_user($path);

	// Get the file
	$file = get_file($path, 'places-save', new stdClass());

	// GET Request
	if ($method === 'GET') {

		// If there's no ID, send the whole data set
		if (empty($id)) {
			http_response_code(200);
			die(json_encode($file));
		}

		// Otherwise, look for a specific item in the API
		if (!empty($file->{$id})) {
			http_response_code(200);
			die(json_encode($file->{$id}));
		}

		// If there's no item, throw an error
		http_response_code(404);
		die('No item with this ID exists.');

	}

	extract_data();

	// POST/PUT Request
	if ($method === 'POST' || $method === 'PUT') {

		// If no ID was provided, throw an error
		if (empty($id)) {
			http_response_code(400);
			die('Please provide an ID');
		}

		// Add or update the item
		$file->{$id} = $_POST;

		// Save to database
		set_file($path, 'places-save', $file);

		// Return data
		http_response_code(200);
		die(json_encode($file->{$id}));

	}

	// DELETE Request
	if ($method === 'DELETE') {

		// If no ID was provided, reset entire file
		if (empty($id)) {
			set_file($path, 'places-save', new stdClass());
			http_response_code(200);
			die('{}');
		}

		// Delete the item
		unset($file->{$id});

		// Save to database
		set_file($path, 'places-save', $file);

		// Return data
		http_response_code(200);
		die(json_encode($file));

	}

	// All other requests
	http_response_code(405);
	die('Method not allowed');
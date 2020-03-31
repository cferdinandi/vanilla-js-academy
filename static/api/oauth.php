<?php

	if (empty($_POST['email']) || empty($_POST['password'])) {
		http_response_code(500);
		die('Please provide a valid email address and password.');
	}

	http_response_code(200);
	echo(json_encode(array(
		'access_token' => md5($_POST['email']),
		'expires' => 1000 * 60 * 60 * 24,
		'token_type' => 'Bearer'
	)));
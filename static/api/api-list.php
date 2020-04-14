<?php

	// Return an array of API info
	function get_api_help ($user) {
		$url = 'https://vanillajsacademy.com/api/';
		return array(
			'dogs' => array(
				'description' => 'A list of adoptable dogs.',
				'endpoint' => $url . 'dogs.json',
				'publicEndpoint' => $url . 'dogs.json',
				'methods' => array('GET'),
				'authentication' => null,
			),
			'echo' => array(
				'description' => 'A test endpoint that returns any data you send. Does not store data.',
				'endpoint' => $url . 'echo.php',
				'publicEndpoint' => null,
				'methods' => array('POST', 'PUT'),
				'authentication' => array('Bearer'),
			),
			'notes' => array(
				'description' => 'Your saved notes.',
				'endpoint' => $url . 'notes.php',
				'publicEndpoint' => null,
				'methods' => array('GET', 'POST', 'PUT', 'DELETE'),
				'authentication' => array('Bearer'),
			),
			'oauth' => array(
				'description' => 'Get an OAuth Bearer token for use with API requests.',
				'endpoint' => $url . 'outh.php',
				'publicEndpoint' => null,
				'methods' => array('GET', 'POST', 'PUT', 'DELETE'),
				'authentication' => array('Basic', 'Bearer'),
			),
			'pirates' => array(
				'description' => 'Get a list of articles for the Scuttlebutt pirate publication.',
				'endpoint' => $url . 'pirates.json',
				'publicEndpoint' => $url . 'pirates.json',
				'methods' => array('GET'),
				'authentication' => null,
			),
			'scavengerHuntSingle' => array(
				'description' => 'A single scavenger hunt list.',
				'endpoint' => $url . 'scavenger-hunt.json',
				'publicEndpoint' => $url . 'scavenger-hunt.json',
				'methods' => array('GET'),
				'authentication' => null,
			),
			'scavengerHunt' => array(
				'description' => 'Scavenger hunt lists.',
				'endpoint' => $url . 'scavenger-hunt.php',
				'publicEndpoint' => $url . 'scavenger-hunt.php' . '?public=' . $user,
				'methods' => array('GET', 'POST', 'PUT', 'DELETE'),
				'authentication' => array('Bearer'),
			),
		);
	}
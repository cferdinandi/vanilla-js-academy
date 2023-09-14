<?php

	http_response_code(403);
	die(json_encode([
		'status' => 'Shall Not Pass',
		'message' => 'You do not have access to this information, young hobbit'
	]));
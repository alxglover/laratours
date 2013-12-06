<?php
	// wrapper for skype js, allows mod_deflate compression to be active

	$skype_api_url = 'http://www.skypeassets.com/i/scom/js/skype-uri.js';

	$skype_api_js = file_get_contents($skype_api_url);

	header('Content-Type: text/javascript'); // send javascript header

	echo $skype_api_js;
	
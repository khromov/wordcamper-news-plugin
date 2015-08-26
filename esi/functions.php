<?php

/**
 * Authenticates a WP user over ESI by their secret cookie
 *
 * @return bool
 */
function wordcamper_auth_user_esi() {

	//Grab headers
	$headers = getallheaders();

	if(isset($headers['X-Subscriberinfo'])) {
		$cookie_content = urldecode($headers['X-Subscriberinfo']);
	}
	else {
		return false;
	}

	$salt = 'super-secret-hash';

	$cookie_imploded = explode('|', $cookie_content);
	$username = isset($cookie_imploded[1]) ? $cookie_imploded[1] : '';
	$hash = isset($cookie_imploded[2]) ? $cookie_imploded[2] : '';

	$expected_hash = md5($username . $salt);

	if($hash === $expected_hash) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * FIXME: Implement extra parameters
 *
 * @param $action
 * @param array $extra_params
 * @return string
 */
function wordcamper_esi_get_api_response($action, $extra_params = array()) {
	//FIXME: No hard coding, get from config
	return json_decode(file_get_contents('http://cache.hhvm.wordcamper-news.dev/?wordcampers_internal_api=1&action=' . $action));
}
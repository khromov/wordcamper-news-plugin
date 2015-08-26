<?php
/**
* Plugin Name: WordCamper News Utility Plugin
* Plugin URI:
* Description: Adds widgets and ESI endpoints
* Author:      khromov
* Author URI:  http://snippets.khromov.se
* Version:     1.0
* License:     GPLv2
*/

include 'widgets/ESILogin.php';
include 'classes/APIEndpoints.php';

//Create endpoints
$wc_api_endpoint = new WordCamper_API_Endpoints();

function wordcamper_generate_user_hash($username) {
	return md5($username . 'super-secret-hash');
}

function wordcamper_set_subscriber_cookie($username) {
	$user_secret_key = wordcamper_generate_user_hash($username); //TODO: in config!
	setcookie( 'wp_subscriber', "1|{$username}|{$user_secret_key}", time() + YEAR_IN_SECONDS, SITECOOKIEPATH, null, wcn_is_https_request());
}

function wordcamper_delete_subscriber_cookie($username) {
	$user_secret_key = wordcamper_generate_user_hash($username); //TODO: in config!
	setcookie( 'wp_subscriber', "1|{$username}|{$user_secret_key}", time() - 3600, SITECOOKIEPATH, null, wcn_is_https_request());
}

/**
 * Set appropriate cookies
 */
add_action('init', function() {
	//var_dump(is_user_logged_in());die();

	if(is_user_logged_in()) {

		$user = wp_get_current_user();

		//Check all roles
		foreach ($user->roles as $role) {

			if ($role === 'subscriber') {
				wordcamper_set_subscriber_cookie($user->user_login);
				break;
			}
		}
	}
});

/**
 * Redirect to main page on logout and dump subscriber cookie
 */
add_action('wp_logout', function() {

	setcookie( 'wp_not_subscriber', '1', time() - 3600, SITECOOKIEPATH, null, wcn_is_https_request());
	wp_redirect( home_url() );
	exit();
});

/**
 * Whether this is a HTTPS request or not
 *
 * @return bool
 */
function wcn_is_https_request() {
	return ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) );
}

/**
 * Set a cookie called "wp_user_is_subscriber" on Subscriber login
 */
add_action('wp_login', function($user_login, $user) {

	if(is_user_logged_in()) {

		$user = wp_get_current_user();

		//Check all roles
		foreach ($user->roles as $role) {

			if ($role === 'subscriber') {
				wordcamper_set_subscriber_cookie($user->user_login);
				break;
			}
		}
	}

}, 10, 2);


/**
 * Expire the wp_user_is_subscriber cookie on logout
 */
add_action('wp_logout', function() {
	delete_all_cookies();
	//setcookie( 'wp_user_is_subscriber', '1', time() - 3600, null, wcn_is_https_request() );
});

/** http://stackoverflow.com/questions/2310558/how-to-delete-all-cookies-of-my-website-in-php **/
function delete_all_cookies() {
	if (isset($_SERVER['HTTP_COOKIE'])) {
		$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
		foreach($cookies as $cookie) {
			$parts = explode('=', $cookie);
			$name = trim($parts[0]);
			setcookie($name, '', time()-1000);
			setcookie($name, '', time()-1000, '/');
		}
	}
}
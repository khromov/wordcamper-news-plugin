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

/**
 * Set appropriate cookies
 */
add_action('init', function() {
	//var_dump(is_user_logged_in());die();

	if(is_user_logged_in()) {

		$user = wp_get_current_user();

		$subscriber = false;

		//Check all roles
		foreach($user->roles as $role) {

			//Is user subscriber?
			if($role === 'subscriber') {
				$subscriber = true;
				//TODO: Look over security implications of this (is httponly flag enough?)
			}
		}

		if(!$subscriber) {
			//We looped all roles, user is not subscriber = we can set the non-subscriber cookie
			setcookie( 'wp_not_subscriber', '1', time() + YEAR_IN_SECONDS, SITECOOKIEPATH, null, wcn_is_https_request());
		}

		//Sanity check if user logged in
		//if(is_a($user, 'WP_User') && $user->ID !== 0) {
		//	var_dump($user->ID);
		//}
		//setcookie( 'wp_not_subscriber', '1', time() + YEAR_IN_SECONDS, SITECOOKIEPATH, null, wcn_is_https_request() );
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

	//Valid user login
	if(is_a($user, 'WP_User')) {

		$subscriber = false;

		//Check all roles TODO: Remove duplicate dcode
		foreach($user->roles as $role) {

			//Is user subscriber?
			if($role === 'subscriber') {
				$subscriber = true;
			}
		}

		if(!$subscriber) {
			//We looped all roles, user is not subscriber = we can set the non-subscriber cookie
			setcookie( 'wp_not_subscriber', '1', time() + YEAR_IN_SECONDS, SITECOOKIEPATH, null, wcn_is_https_request());
		}
	}

}, 10, 2);


/**
 * Expire the wp_user_is_subscriber cookie on logout
 */
add_action('wp_logout', function() {
	setcookie( 'wp_user_is_subscriber', '1', time() - 3600, null, wcn_is_https_request() );
});

/**
 * Add endpoints for grabbing our data
 */
add_action('wp_logout', function() {
	setcookie( 'wp_user_is_subscriber', '1', time() - 3600, null, wcn_is_https_request() );
});
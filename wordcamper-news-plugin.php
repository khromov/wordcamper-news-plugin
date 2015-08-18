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

	//var_dump($user_login);
	//var_dump($user);

	//Valid user login
	if(is_a($user, 'WP_User')) {

		//Check all roles
		foreach($user->roles as $role) {

			//Is user subscriber?
			if($role === 'subscriber') {
				setcookie( 'wp_user_is_subscriber', '1', time() + YEAR_IN_SECONDS, SITECOOKIEPATH, null, wcn_is_https_request() );

				//TODO: Look over security implications of this (is httponly flag enough?)
				//setcookie( 'wp_cookie_name', '1', time() + YEAR_IN_SECONDS, SITECOOKIEPATH, null, $secure, true );
			}
		}
	}
	//die();

}, 10, 2);

/**
 * Expire the wp_user_is_subscriber cookie on logout
 */
add_action('wp_logout', function() {
	setcookie( 'wp_user_is_subscriber', '1', time() - 3600, null, wcn_is_https_request() );
});
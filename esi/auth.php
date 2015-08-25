<?php
error_reporting(-1);
ini_set('display_errors', 'On');

/* TODO: Read cookie value from header */
//..FIXME: May be urlencoded with %7 between pipes
$cookie_content = 'wordpress|1440711208|DbHArvjAoQ0YNc8VIP3wKurLfiu5btdQWvXSGNPcAIH|4366e163595de737985a8a1b6ed3b0941fa46de011f7326ac50d880ad35f4ec5';

/* Define our special variable */
define( 'LOAD_CONFIG_ONLY', true );

/* Load WP config */
require_once '../../../../wp-config.php';

$db = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME .';charset=utf8', DB_USER, DB_PASSWORD);

/* Grab all tokens */
$stmt = $db->query("SELECT * FROM {$table_prefix}usermeta WHERE meta_key = 'session_tokens'");
$user_tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($user_tokens);

/** Prepare array for valid logins **/
$valid_tokens = array();

/** Assemble all tokens to a map **/
foreach($user_tokens as $single_user_tokens) {

	/** @var setup tokens $uid */
	$uid = isset($single_user_tokens['user_id']) ? (int)$single_user_tokens['user_id'] : null;
	$tokens = isset($single_user_tokens['meta_value']) ? unserialize($single_user_tokens['meta_value']) : null;

	foreach($tokens as $token_key => $data) {
		$valid_tokens[] = ($token_key); //hash_token
	}
}

$cookie_content_parsed = wp_parse_frontend_cookie($cookie_content);
var_dump($cookie_content_parsed);

var_dump(wp_validate_auth_cookie($cookie_content));
die();

/**
 * borrowed
 *
 * @param string $cookie
 * @param string $scheme
 * @return array|bool
 */
function wp_parse_frontend_cookie($cookie = '', $scheme = '') {

	$cookie_name = 'wordpress_logged_in_1e92f7e8eb207ca87fb2192042e19337';

	$cookie_elements = explode('|', $cookie);
	if ( count( $cookie_elements ) !== 4 ) {
		return false;
	}

	list( $username, $expiration, $token, $hmac ) = $cookie_elements;

	return compact( 'username', 'expiration', 'token', 'hmac', 'scheme' );
}

function wp_validate_auth_cookie($cookie = '', $scheme = '') {

	if ( ! $cookie_elements = wp_parse_frontend_cookie($cookie, $scheme) ) {
		return false;
	}

	var_dump("here");

	$scheme = $cookie_elements['scheme'];
	$username = $cookie_elements['username'];
	$hmac = $cookie_elements['hmac'];
	$token = $cookie_elements['token'];
	$expired = $expiration = $cookie_elements['expiration'];

	// Quick check to see if an honest cookie has expired
	if ( $expired < time() ) {
		return false;
	}

	global $db;
	global $table_prefix;

	//Grab user in cookie
	$stmt = $db->prepare("SELECT * FROM {$table_prefix}users WHERE user_login = ?");
	$stmt->execute(array('wordpress'));
	$matching_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

	var_dump($matching_users);

	//$user = get_user_by('login', $username);

	//Diss if user doesn't exist
	if ( ! isset($matching_users[0]) ) {
		return false;
	} else {
		$user = $matching_users[0];
	}


	$pass_frag = substr($user['user_pass'], 8, 4);
	var_dump($pass_frag);
	die();

	$key = wp_hash( $username . '|' . $pass_frag . '|' . $expiration . '|' . $token, $scheme );

	// If ext/hash is not present, compat.php's hash_hmac() does not support sha256.
	$algo = function_exists( 'hash' ) ? 'sha256' : 'sha1';
	$hash = hash_hmac( $algo, $username . '|' . $expiration . '|' . $token, $key );

	if ( ! hash_equals( $hash, $hmac ) ) {
		/**
		 * Fires if a bad authentication cookie hash is encountered.
		 *
		 * @since 2.7.0
		 *
		 * @param array $cookie_elements An array of data for the authentication cookie.
		 */
		do_action( 'auth_cookie_bad_hash', $cookie_elements );
		return false;
	}

	$manager = WP_Session_Tokens::get_instance( $user->ID );
	//var_dump($_COOKIE);
	//var_dump($token);die();
	if ( ! $manager->verify( $token ) ) {
		do_action( 'auth_cookie_bad_session_token', $cookie_elements );
		return false;
	}

	// AJAX/POST grace period set above
	if ( $expiration < time() ) {
		$GLOBALS['login_grace_period'] = 1;
	}

	/**
	 * Fires once an authentication cookie has been validated.
	 *
	 * @since 2.7.0
	 *
	 * @param array   $cookie_elements An array of data for the authentication cookie.
	 * @param WP_User $user            User object.
	 */
	do_action( 'auth_cookie_valid', $cookie_elements, $user );

	return $user->ID;
}


/**
 * Borrowed from WP_Session_Tokens->verify();
 *
 * @return string
 */
function hash_token($token) {
	if ( function_exists( 'hash' ) ) {
		return hash( 'sha256', $token );
	} else {
		return sha1( $token );
	}
}

var_dump($valid_tokens);

echo rand(0, 200);
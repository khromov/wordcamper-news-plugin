<?php
class WordCamper_API_Endpoints {

	function __construct() {
		add_action('template_redirect', array($this, 'endpoints'));
	}

	function endpoints() {
		$params = array_merge(
			array(
				'wordcampers_internal_api' => '0',
				'action' => ''
			),
			$_GET
		);

		if($params['wordcampers_internal_api'] === '1') {

			header('Content-Type: application/json');

			$data = array();

			if($params['action'] === 'login_form') {
				$args = array(
						'echo'           => true,
						'redirect'       => home_url('/wp-admin/'),
						'form_id'        => 'loginform',
						'label_username' => __( 'Username' ),
						'label_password' => __( 'Password' ),
						'label_remember' => __( 'Remember Me' ),
						'label_log_in'   => __( 'Log In' ),
						'id_username'    => 'user_login',
						'id_password'    => 'user_pass',
						'id_remember'    => 'rememberme',
						'id_submit'      => 'wp-submit',
						'remember'       => true,
						'value_username' => NULL,
						'value_remember' => true
				);

				ob_start();
				wp_login_form($args);
				$data['login_form'] = ob_get_clean();
			}
			else if($params['action'] === 'logout_form') {
				ob_start();
				?>
				<div class="aa_logout">
					Hello,
					<div class="aa_logout_user">
						$user_login, You are already logged in.
					</div>
					<a id="wp-submit" href="<?php echo wp_logout_url(); ?>" title="Logout">
						Logout
					</a>
				</div>
				<?php
				$data['logout_form'] = ob_get_clean();
			}

			echo json_encode($data);

			die();
		}


	}
}
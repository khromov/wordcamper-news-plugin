<?php


/**
 * Register the widget
 */
add_action('widgets_init', create_function('', 'return register_widget("Widget_ESI_Login");'));

/**
 * Class Widget_Better_Starter_Widget
 */
class Widget_ESI_Login extends WP_Widget
{
	/** Basic Widget Settings */
	const WIDGET_NAME = "- ESI Login and info";
	const WIDGET_DESCRIPTION = "This is the description";

	var $textdomain;
	var $fields;

	/**
	 * Construct the widget
	 */
	function __construct()
	{
		//We're going to use $this->textdomain as both the translation domain and the widget class name and ID
		$this->textdomain = strtolower(get_class($this));

		//Add fields
		$this->add_field('title', 'Enter title', '', 'text');

		//Translations
		load_plugin_textdomain($this->textdomain, false, basename(dirname(__FILE__)) . '/languages' );

		//Init the widget
		parent::__construct($this->textdomain, __(self::WIDGET_NAME, $this->textdomain), array( 'description' => __(self::WIDGET_DESCRIPTION, $this->textdomain), 'classname' => $this->textdomain));
	}

	/**
	 * Widget frontend
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);

		/* Before and after widget arguments are usually modified by themes */
		echo $args['before_widget'];

		if (!empty($title))
			echo $args['before_title'] . $title . $args['after_title'];

		/* Widget output here */
		$this->widget_output($args, $instance);

		/* After widget */
		echo $args['after_widget'];
	}

	/**
	 * This function will execute the widget frontend logic.
	 * Everything you want in the widget should be output here.
	 */
	private function widget_output($args, $instance)
	{
		extract($instance);

		/**
		 * This is where you write your custom code.
		 */
		?>
				<esi:include src="/wp-content/plugins/wordcamper-news-plugin/esi/login-form.php" />

        <?php
        /*
        global $user_login;
        if(isset($_GET['login']) && $_GET['login'] == 'failed')
        {
            ?>
	            <div class="aa_error">
		            <p>FAILED: Try again!</p>
	            </div>
            <?php
        }

				if (is_user_logged_in())
				{
						echo '<div class="aa_logout"> Hello, <div class="aa_logout_user">', $user_login, '. You are already logged in.</div><a id="wp-submit" href="', wp_logout_url(), '" title="Logout">Logout</a></div>';
				}
				else
				{
							wp_login_form($args);
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
				}
        */
			?>

			</section>
			<!-- /section -->
		<?php
	}

	/**
	 * Widget backend
	 *
	 * @param array $instance
	 * @return string|void
	 */
	public function form( $instance )
	{
		/* Generate admin for fields */
		foreach($this->fields as $field_name => $field_data)
		{
			if($field_data['type'] === 'text'):
				?>
				<p>
					<label for="<?php echo $this->get_field_id($field_name); ?>"><?php _e($field_data['description'], $this->textdomain ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id($field_name); ?>" name="<?php echo $this->get_field_name($field_name); ?>" type="text" value="<?php echo esc_attr(isset($instance[$field_name]) ? $instance[$field_name] : $field_data['default_value']); ?>" />
				</p>
				<?php
			//elseif($field_data['type'] == 'textarea'):
			//You can implement more field types like this.
			else:
				echo __('Error - Field type not supported', $this->textdomain) . ': ' . $field_data['type'];
			endif;
		}
	}

	/**
	 * Adds a text field to the widget
	 *
	 * @param $field_name
	 * @param string $field_description
	 * @param string $field_default_value
	 * @param string $field_type
	 */
	private function add_field($field_name, $field_description = '', $field_default_value = '', $field_type = 'text')
	{
		if(!is_array($this->fields))
			$this->fields = array();

		$this->fields[$field_name] = array('name' => $field_name, 'description' => $field_description, 'default_value' => $field_default_value, 'type' => $field_type);
	}

	/**
	 * Updating widget by replacing the old instance with new
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update($new_instance, $old_instance)
	{
		return $new_instance;
	}
}
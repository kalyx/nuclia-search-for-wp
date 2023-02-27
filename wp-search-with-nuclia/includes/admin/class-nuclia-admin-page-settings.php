<?php
/**
 * Nuclia_Admin_Page_Settings class file.
 *
 * @since   1.0.0
 *
 * @package Kalyx\WPSWN
 */

/**
 * Class Nuclia_Admin_Page_Settings
 *
 * @since 1.0.0
 */
class Nuclia_Admin_Page_Settings {

	/**
	 * The Nuclia_Plugin instance.
	 *
	 * @since  1.0.0
	 *
	 * @var Nuclia_Plugin
	 */
	private $plugin;
	
	/**
	 * The Nuclia_Settings instance.
	 *
	 * @since  1.0.0
	 *
	 * @var Nuclia_Settings
	 */
	private $settings;
	
	/**
	 * Admin page slug.
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	private $slug = 'nuclia-settings';

	/**
	 * Admin page capabilities.
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	private $capability = 'manage_options';

	
	/**
	 * Nuclia_Admin_Page_Settings constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param Nuclia_Plugin $plugin The Nuclia_Plugin instance.
	 */
	public function __construct( Nuclia_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->settings = $plugin->get_settings();
		
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_init', array( $this, 'add_settings' ) );
		add_action( 'admin_notices', array( $this, 'display_errors' ) );

		// Display a link to this page from the plugins page.
		add_filter( 'plugin_action_links_' . NUCLIA_PLUGIN_BASENAME, array( $this, 'add_action_links' ) );
	}

	/**
	 * Add action links.
	 *
	 * @since  1.0.0
	 *
	 * @param array $links Array of action links.
	 *
	 * @return array
	 */
	public function add_action_links( array $links ) {
		return array_merge(
			$links,
			array(
				'<a href="' . esc_url( admin_url( 'admin.php?page=' . $this->slug ) ) . '">' . esc_html__( 'Settings', 'wp-search-with-nuclia' ) . '</a>',
			)
		);
	}

	/**
	 * Add admin menu page.
	 *
	 * @since  1.0.0
	 *
	 * @return string|void The resulting page's hook_suffix.
	 */
	public function add_page() {
		
		add_menu_page(
			'WP Search With Nuclia',
			esc_html__( 'Nuclia Search', 'wp-search-with-nuclia' ),
			$this->capability,
			$this->slug,
			array( $this, 'display_page' ),
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSI3LjE0OCAxMy40NTYgOTEuMDM1IDk0LjAzNyIgd2lkdGg9IjkxLjAzNSIgaGVpZ2h0PSI5NC4wMzciIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgPGRlZnM+CiAgICA8c3R5bGU+LmNscy0xe2ZpbGw6I2ZmZDkxYjt9LmNscy0ye2ZpbGw6IzI1MDBmZjt9LmNscy0ze2ZpbGw6I2ZmMDA2YTt9PC9zdHlsZT4KICA8L2RlZnM+CiAgPHBhdGggY2xhc3M9ImNscy0xIiBkPSJNOTEuNjYsMzUuNzgsNTMuNDcsMTQuNDNhLjE5LjE5LDAsMCwwLS4xOCwwTDE0Ljk0LDM1LjQ5YS4xOS4xOSwwLDAsMCwwLC4zM0w1MC40LDU1LjUyYS4xOS4xOSwwLDAsMCwuMTgsMCw1LjQ3LDUuNDcsMCwwLDEsNS43MS4xMy4xNy4xNywwLDAsMCwuMTgsMEw5MS42NiwzNi4xMUEuMTkuMTksMCwwLDAsOTEuNjYsMzUuNzhaIi8+CiAgPHBhdGggY2xhc3M9ImNscy0yIiBkPSJNNTguNzcsNjAuMDhhLjcxLjcxLDAsMCwxLDAsLjE0QTUuNDcsNS40NywwLDAsMSw1Niw2NWEuMTYuMTYsMCwwLDAtLjA5LjE1djQxYS4xOS4xOSwwLDAsMCwuMjguMTZMOTQuNDEsODUuMTFhLjIuMiwwLDAsMCwuMDktLjE3VjQwLjU1YS4xOC4xOCwwLDAsMC0uMjctLjE3WiIvPgogIDxwYXRoIGNsYXNzPSJjbHMtMyIgZD0iTTUxLjA1LDY1LjI5djQxYS4xOC4xOCwwLDAsMS0uMjcuMTZMMTIuMjEsODVhLjIxLjIxLDAsMCwxLS4xLS4xN1Y0MC4yN2EuMTkuMTksMCwwLDEsLjI4LS4xNkw0Ny45LDU5LjgzYzAsLjEzLDAsLjI2LDAsLjM5QTUuNDYsNS40NiwwLDAsMCw1MSw2NS4xMy4xOC4xOCwwLDAsMSw1MS4wNSw2NS4yOVoiLz4KPC9zdmc+'
		);

	}

	/**
	 * Add settings.
	 *
	 * @since  1.0.0
	 */
	public function add_settings() {
		add_settings_section(
			'nuclia_section_settings',
			null,
			array( $this, 'print_settings_section' ),
			$this->slug
		);

		add_settings_field(
			'nuclia_zone',
			esc_html__( 'Zone', 'wp-search-with-nuclia' ),
			array( $this, 'zone_callback' ),
			$this->slug,
			'nuclia_section_settings'
		);

		add_settings_field(
			'nuclia_kbid',
			esc_html__( 'Knowledge Box ID', 'wp-search-with-nuclia' ),
			array( $this, 'kbid_callback' ),
			$this->slug,
			'nuclia_section_settings'
		);
		
		add_settings_field(
			'nuclia_token',
			esc_html__( 'Token', 'wp-search-with-nuclia' ),
			array( $this, 'token_callback' ),
			$this->slug,
			'nuclia_section_settings'
		);


		add_settings_field(
			'nuclia_indexable_post_types',
			esc_html__( 'Post types to index', 'wp-search-with-nuclia' ),
			array( $this, 'indexable_post_types_callback' ),
			$this->slug,
			'nuclia_section_settings'
		);
		
		register_setting(
			'nuclia_settings',
			'nuclia_zone',
			[
				'type' => 'text',
				'sanitize_callback' => array( $this, 'sanitize_zone' )
			]
		);
		
		register_setting(
			'nuclia_settings',
			'nuclia_kbid',
			[
				'type' => 'text',
				'sanitize_callback' => array( $this, 'sanitize_kbid' )
			]
		);	
			
		register_setting(
			'nuclia_settings',
			'nuclia_token',
			[
				'type' => 'text',
				'sanitize_callback' => array( $this, 'sanitize_token' )
			]
		);

		register_setting(
			'nuclia_settings',
			'nuclia_indexable_post_types',
			[
				'type' => 'array',
				'sanitize_callback' => array( $this, 'sanitize_indexable_post_types' )
			]
		);
	}

	/**
	 * Zone callback.
	 *
	 * @since  1.0.0
	 */
	public function zone_callback() {
		$settings      = $this->plugin->get_settings();
		$setting       = $settings->get_zone();
		?>
		<input type="text" name="nuclia_zone" class="regular-text" value="<?php echo esc_attr( $setting ); ?>"/>
		<p class="description" id="home-description"><?php esc_html_e( 'Your Nuclia Zone. Default: europe-1', 'wp-search-with-nuclia' ); ?></p>
		<?php
	}

	/**
	 * Token callback.
	 *
	 * @since  1.0.0
	 */
	public function token_callback() {
		$settings      = $this->plugin->get_settings();
		$setting       = $settings->get_token();
		?>
		<input type="password" name="nuclia_token" class="regular-text" value="<?php echo esc_attr( $setting ); ?>" />
		<p class="description" id="home-description"><?php esc_html_e( 'Your Nuclia Service Access token (kept private).', 'wp-search-with-nuclia' ); ?></p>
		<?php
	}

	/**
	 * Admin Knowledge box UID callback.
	 *
	 * @since  1.0.0
	 */
	public function kbid_callback() {
		$settings      = $this->plugin->get_settings();
		$setting       = $settings->get_kbid();
		?>
		<input type="text" name="nuclia_kbid" class="regular-text" value="<?php echo esc_attr( $setting ); ?>" />
		<p class="description" id="home-description"><?php esc_html_e( 'Your Nuclia Knowledge box UID (must be public).', 'wp-search-with-nuclia' ); ?></p>
		<?php
	}

	/**
	 * Indexable post_types callback.
	 *
	 * @since  1.0.0
	 */
	public function indexable_post_types_callback() {
		// current value
		$indexable_post_types = $this->plugin->get_indexable_post_types();
		
		// registered searchable post types
		$args = apply_filters( 'nuclia_searchable_post_types',
			array( 
				'public' => true,
				'exclude_from_search' => false
			)
		);
		
		$searchable_post_types = get_post_types(
			$args,
			'names'
		);
		
		foreach ( $searchable_post_types as $post_type ) :
		?>
		<p>
        	<label for="nuclia_<?php echo $post_type ?>_enable">
				<input id="nuclia_<?php echo $post_type ?>_enable" type="checkbox" name="nuclia_indexable_post_types[<?php echo $post_type; ?>]" value="1" <?php echo !empty( $indexable_post_types[$post_type] ) ? 'checked="checked"' : ''; ?>/>
				&nbsp;<?php echo $this->get_post_type_name( $post_type ); ?>
            </label>
		</p>
		<?php
		endforeach;
		$settings = $this->plugin->get_settings();
		if ( $settings->get_api_is_reachable() ) :
		?>
        <div class="notice notice-success"> 
        <p><strong><span class="dashicons dashicons-saved" style="color:#090;"></span> <?php _e("You can start indexing your site.", 'wp-search-with-nuclia' ); ?></strong></p>
        </div>
        <?php
		endif;
	}

	/**
	 * Get post type name
	 *
	 * @since   1.0.0
	 *
	 * @param string $post_type The post type slug.
	 *
	 * @return array
	 */
	public function get_post_type_name( $post_type ) {
		
		$post_type_object = get_post_type_object( $post_type );
		
		if ( $post_type_object !== NULL ) {
			return $post_type_object->labels->name;
		};
		
		return '';
	}
		
	/**
	 * Sanitize zone.
	 *
	 * @since  1.0.0
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_zone( $value ) {

		$value = sanitize_text_field( $value );

		if ( empty( $value ) ) {
			add_settings_error(
				'nuclia_settings',
				'empty',
				esc_html__( 'Zone should not be empty.', 'wp-search-with-nuclia' )
			);

		}

		return $value;
	}

	/**
	 * Sanitize Service Access token.
	 *
	 * @since  1.0.0
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_token( $value ) {

		$value = sanitize_text_field( $value );

		if ( empty( $value ) ) {
			add_settings_error(
				'nuclia_settings',
				'empty',
				esc_html__( 'Service Access token should not be empty.', 'wp-search-with-nuclia' )
			);
		}

		return $value;
	}

	/**
	 * Sanitize Knowledge box UID.
	 *
	 * @since  1.0.0
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_kbid( $value ) {

		$value = sanitize_text_field( $value );

		if ( empty( $value ) ) {
			add_settings_error(
				'nuclia_settings',
				'empty',
				esc_html__( 'Knowledge box UID should not be empty', 'wp-search-with-nuclia' )
			);
		}

		$errors = get_settings_errors( 'nuclia_settings' );

		// @todo Not 100% clear why this is returning here.
		if ( ! empty( $errors ) ) {
			return $value;
		}

		$settings = $this->plugin->get_settings();

		$valid_credentials = true;
		try {
			$this->is_valid_credentials( $settings->get_zone(), $settings->get_kbid() );
		} catch ( Exception $exception ) {
			$valid_credentials = false;
			add_settings_error(
				'nuclia_settings',
				'login_exception',
				$exception->getMessage()
			);
		}

		if ( ! $valid_credentials ) {
			add_settings_error(
				'nuclia_settings',
				'no_connection',
				esc_html__(
					'We were unable to authenticate you against the Nuclia servers with the provided information. Please ensure that you used a valid Zone and Knowledge Box ID.',
					'wp-search-with-nuclia'
				)
			);
			$settings->set_api_is_reachable( false );
		} else {
			if ( ! $this->is_valid_token( $settings->get_zone(), $settings->get_kbid(), $settings->get_token() ) ) {
				add_settings_error(
					'nuclia_settings',
					'wrong_token',
					esc_html__(
						'It looks like your token is wrong.',
						'wp-search-with-nuclia'
					)
				);
				$settings->set_api_is_reachable( false );
			} else {
				add_settings_error(
					'nuclia_settings',
					'connection_success',
					esc_html__( 'We succesfully managed to connect to the Nuclia servers with the provided information.', 'wp-search-with-nuclia' ),
					'updated'
				);
				$settings->set_api_is_reachable( true );
			}
		}

		return $value;
	}

	/**
	 * Sanitize indexable post_types
	 *
	 * @since  1.0.0
	 *
	 * @param array $value The data to sanitize.
	 *
	 * @return array
	 */
	public function sanitize_indexable_post_types( $value ) {
		
		$settings = $this->plugin->get_settings();

		if ( is_array( $value ) ) {

			foreach( $value as $post_type => $checked ) {
				// remove disabled post types
				if ( !$checked ) {
					unset( $value[$post_type] );
				}
			}

		};
		
		// no post type selected, display a notice
		if ( empty( $value ) ) {
			add_settings_error(
				'nuclia_settings',
				'nothing_to_index',
				esc_html__(
					'No post type selected. No indexing will take place.',
					'wp-search-with-nuclia'
				)
			);
			$settings->set_api_is_reachable( false );
		};
		
		return $value;
	}
	
	/**
	 * Assert that the credentials are valid.
	 *
	 * @since  1.0.0
	 *
	 * @param string $zone 		  The Nuclia Zone.
	 * @param string $kbid        The Nuclia KBID.
	 *
	 * @return bool
	 */
	public static function is_valid_credentials( $zone, $kbid ) {
		$endpoint = sprintf( 'https://%1s.nuclia.cloud/api/v1/kb/%2s',$zone,$kbid);
		$response = wp_remote_get( $endpoint );
		if ( is_wp_error( $response ) ) {
			//bad zone
			throw new Exception(
				__('Cannot connect to Nuclia API, please check your Nuclia zone : '.$response->get_error_message(), 'wp-search-with-nuclia')
			);
			return false;
		}
		
		$response_code 	= wp_remote_retrieve_response_code( $response );
		if( $response_code === 200 ) {
			return;
		} elseif( $response_code === 422 ) {
			throw new Exception(
				__('Cannot connect to Nuclia API, please check your Knowledge Box ID', 'wp-search-with-nuclia')
			);
			return false;
		} else {
			throw new Exception(
				__('Cannot connect to Nuclia API, no response from the server', 'wp-search-with-nuclia')
			);
			return false;
		};
	}

	/**
	 * Check if the token is valid.
	 *
	 * @since  1.0.0
	 *
	 * @param string $zone The Nuclia Zone.
	 * @param string $token The Nuclia Search API Key.
	 *
	 * @return bool
	 */
	public static function is_valid_token( $zone, $kbid, $token ) {

		$endpoint = sprintf( 'https://%1s.nuclia.cloud/api/v1/kb/%2s',$zone,$kbid);
		$args = array(
			'headers' => array(
				'X-STF-Serviceaccount' => 'Bearer ' . $token ),
				//'x-skip-store' => 
		);
		$response = wp_remote_get( $endpoint, $args );

		if ( is_wp_error( $response ) ) {
			return false;
		};
		$response_code 	= wp_remote_retrieve_response_code( $response );
		return $response_code === 200;
	}	
	/**
	 * Display the page.
	 *
	 * @since  1.0.0
	 */
	public function display_page() {
		require_once dirname( __FILE__ ) . '/partials/page-settings.php';
	}

	/**
	 * Display errors.
	 *
	 * @since  1.0.0
	 */
	public function display_errors() {
		settings_errors( 'nuclia_settings' );
	}

	/**
	 * Print the settings section header.
	 *
	 * @since  1.0.0
	 */
	public function print_settings_section() {
		echo '<p>' . wp_kses_post( sprintf( __('The zone, token, knowledge base id can be found or configured at your Nuclia cloud account. Please sign up at %1s and sign in at %2s'),
			'<a href="https://nuclia.cloud/user/login" target="blank">https://nuclia.cloud/user/login</a>',
            '<a href="https://nuclia.com/sign-up" target="blank">https://nuclia.com/sign-up/</a>'
        )) . '</p>';
		echo '<p>' . esc_html__( 'Once you provide your Nuclia Zone and API key, this plugin will be able to securely communicate with Nuclia servers.', 'wp-search-with-nuclia' ) . ' ' . esc_html__( 'We ensure your information is correct by testing them against the Nuclia servers upon save.', 'wp-search-with-nuclia' ) . '</p>';
		$settings = $this->plugin->get_settings();
		$zone = $settings->get_zone() ?: 'your-zone';
		$kbid = $settings->get_kbid() ?: 'your-kbid';
		echo '<h3>Widget</h3>';
		echo '<p>'.__( 'You can put the Nuclia Searchbox widget in any widget area.', 'wp-search-with-nuclia').'</p>';
		echo '<h3>Shortcode</h3>';
		echo '<p>';
		echo esc_html__( 'Copy and paste this shortcode into any content. For the features, you can choose:').'<br>';
		echo ' - "navigateToLink" : '. __("clicking on a result will open the original page rather than rendering it in the viewer." , 'wp-search-with-nuclia' ).'<br>';
		echo ' - "permalink" : '. __("add extra parameters in URL allowing direct opening of a resource or search results." , 'wp-search-with-nuclia' ).'<br>';
		echo ' - "suggestions" : '. __("suggest results while typing search query." , 'wp-search-with-nuclia' );
		echo '</p>';
		echo '<p><code>[nuclia_searchbox zone="'.$zone.'" kbid="'.$kbid.'" features="navigateToLink,permalink,suggestions"]</code></p>';
		echo '<h3>Your Nuclia credentials</h3>';
	}
	
}

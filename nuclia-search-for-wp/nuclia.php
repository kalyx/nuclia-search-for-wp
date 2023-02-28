<?php

/**

 * Plugin Name:       Nuclia search for WP

 * Plugin URI:        https://github.com/kalyx/nuclia-search-for-wp

 * Description:       Integrate the powerful Nuclia search service with WordPress

 * Version:           1.0.0

 * Requires at least: 5.6

 * Requires PHP:      7.2

 * Author:            Serge Rauber

 * Author URI:        https://kalyx.fr

 * License:           GNU General Public License v2.0 / MIT License

 * Text Domain:       klx-nuclia-search-for-wp

 * Domain Path:       /languages

 *

 * @since   1.0.0

 * @package kalyx\nuclia-search-for-wp

 */



// Nothing to see here if not loaded in WP context.

if ( ! defined( 'WPINC' ) ) {

	die;

}



// The Nuclia Search plugin version.

define( 'KLX_NUCLIA_VERSION', '1.0.0' );



// The minmum required PHP version.

define( 'KLX_NUCLIA_MIN_PHP_VERSION', '7.2' );



// The minimum required WordPress version.

define( 'KLX_NUCLIA_MIN_WP_VERSION', '5.6' );



define( 'KLX_NUCLIA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );



define( 'KLX_NUCLIA_PLUGIN_URL', plugins_url( '/', __FILE__ ) );



if ( ! defined( 'KLX_NUCLIA_PATH' ) ) {

	define( 'KLX_NUCLIA_PATH', __DIR__ . '/' );

}



/**

 * Check for required PHP version.

 *

 * @since   1.0.0

 *

 * @return bool

 */

function nuclia_php_version_check() {

	if ( version_compare( PHP_VERSION, KLX_NUCLIA_MIN_PHP_VERSION, '<' ) ) {

		return false;

	}

	return true;

}



/**

 * Check for required WordPress version.

 *

 * @since   1.0.0

 *

 * @return bool

 */

function nuclia_wp_version_check() {

	if ( version_compare( $GLOBALS['wp_version'], KLX_NUCLIA_MIN_WP_VERSION, '<' ) ) {

		return false;

	}

	return true;

}



/**

 * Admin notices if requirements aren't met.

 *

 * @since   1.0.0

 */

function nuclia_requirements_error_notice() {



	$notices = [];



	if ( ! nuclia_php_version_check() ) {

		$notices[] = sprintf(

			/* translators: placeholder 1 is minimum required PHP version, placeholder 2 is installed PHP version. */

			esc_html__( 'Nuclia plugin requires PHP %1$s or higher. You’re still on %2$s.', 'klx-nuclia-search-for-wp' ),

			KLX_NUCLIA_MIN_PHP_VERSION,

			PHP_VERSION

		);

	}



	if ( ! nuclia_wp_version_check() ) {

		$notices[] = sprintf(

			/* translators: placeholder 1 is minimum required WordPress version, placeholder 2 is installed WordPress version. */

			esc_html__( 'Nuclia plugin requires at least WordPress in version %1$s, You are on %2$s.', 'klx-nuclia-search-for-wp' ),

			KLX_NUCLIA_MIN_WP_VERSION,

			esc_html( $GLOBALS['wp_version'] )

		);

	}



	foreach ( $notices as $notice ) {

		echo '<div class="notice notice-error"><p>' . esc_html( $notice ) . '</p></div>';

	}

}



/**

 * I18n.

 *

 * @since   1.0.0

 */

function nuclia_load_textdomain() {



	$locale = apply_filters( 'plugin_locale', get_locale(), 'klx-nuclia-search-for-wp' );



	load_textdomain( 'klx-nuclia-search-for-wp', WP_LANG_DIR . '/klx-nuclia-search-for-wp/klx-nuclia-search-for-wp-' . $locale . '.mo' );



	load_plugin_textdomain( 'klx-nuclia-search-for-wp', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );

}



add_action( 'init', 'nuclia_load_textdomain' );









if ( nuclia_php_version_check() && nuclia_wp_version_check() ) {

	

	require_once KLX_NUCLIA_PATH . 'classmap.php';



	$nuclia = Nuclia_Plugin_Factory::create();



} else {

	add_action( 'admin_notices', 'nuclia_requirements_error_notice' );

}


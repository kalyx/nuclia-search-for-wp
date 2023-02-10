<?php
/**
 * Nuclia_Plugin_Factory class file.
 *
 * @since   1.6.0
 * @package Kalyx\WPSWN
 */

/**
 * Class Nuclia_Plugin_Factory
 *
 * Responsible for creating a shared instance of the main Nuclia_Plugin object.
 *
 * @since 1.6.0
 */
class Nuclia_Plugin_Factory {

	/**
	 * Create and return a shared instance of the Nuclia_Plugin.
	 *
	 * @since  1.6.0
	 *
	 * @return Nuclia_Plugin The shared plugin instance.
	 */
	public static function create(): Nuclia_Plugin {

		/**
		 * The static instance to share, else null.
		 *
		 * @since  1.6.0
		 *
		 * @var null|Nuclia_Plugin $plugin
		 */
		static $plugin = null;

		if ( null !== $plugin ) {
			return $plugin;
		}

		$plugin = new Nuclia_Plugin();

		return $plugin;
	}
}

<?php
/**
 * Nuclia_Plugin class file.
 *
 * @since   1.0.0
 *
 * @package Kalyx\WPSWN
 */

/**
 * Class Nuclia_Plugin
 *
 * @since 1.0.0
 */
class Nuclia_Plugin {

	const NAME = 'nuclia';

	/**
	 * Instance of Nuclia_API.
	 *
	 * @since  1.0.0
	 *
	 * @var Nuclia_API
	 */
	public $api;

	/**
	 * Instance of Nuclia_Settings.
	 *
	 * @since  1.0.0
	 *
	 * @var Nuclia_Settings
	 */
	private $settings;

	/**
	 * Array of indexable post types
	 *
	 * @since  1.0.0
	 *
	 * @var array
	 */
	private $indexable_post_types;

	/**
	 * Nuclia_Plugin constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->settings        = new Nuclia_Settings();
		$this->api             = new Nuclia_API( $this->settings );

		add_action( 'init', array( $this, 'load' ), 20 );
	}

	/**
	 * Load.
	 *
	 * @since  1.0.0
	 */
	public function load() {
		// Load admin or public part of the plugin.
		if ( is_admin() ) {
			
			new Nuclia_Admin_Page_Settings( $this );
			
			if ( $this->settings->get_api_is_reachable() ) {
				// post, page and custom post type
				add_action( 'save_post', array( $this, 'create_or_modify_nucliadb_resource' ), 10, 2 );	// $post_id, $post
				// attachment
				add_action( 'add_attachment', array( $this, 'add_or_modify_attachment' ), 10, 1 ); // $post_id
				add_action( 'attachment_updated', array( $this, 'add_or_modify_attachment' ), 10, 1 ); // $post_id
				add_action( 'deleted_post', array( $this, 'create_or_modify_nucliadb_resource' ), 10, 2 ); // $post_id, $post
			}
		}
	
	}

	/**
	 * Get the Nuclia_API.
	 *
	 * @since  1.0.0
	 *
	 * @return Nuclia_API
	 */
	public function get_api() {
		return $this->api;
	}

	/**
	 * Get the Nuclia_Settings.
	 *
	 * @since  1.0.0
	 *
	 * @return Nuclia_Settings
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Get indexable post types.
	 *
	 * @since  1.0.0
	 *
	 * @return array post type names indexable.
	 */
	public function get_indexable_post_types() {
		return $this->settings->get_indexable_post_types();
	}
	
	/** After attachment is added or modified.
	 *
	 * @since   1.0.0
	 *
	 * @param int     $post_ID Attachment ID.
	 */
	 
	public function add_or_modify_attachment ( $post_id ) {
		
		$post = get_post( $post_id );
		
		// we don't index images
		if ( wp_attachment_is_image( $post ) ) return;
		
		// hack attachments are inherit
		$post->post_status = 'publish'; 
		
		$this->create_or_modify_nucliadb_resource( $post_id, $post );
	}
	
	
	/** Create or modify resource.
	 *
	 * @since   1.0.0
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post    WP_Post object.
	 */

	public function create_or_modify_nucliadb_resource( $post_id, $post ) {
				
		// auto-save
		if( wp_is_post_autosave($post) ) return;
		
		nuclia_log( 'ID : '.$post_id );
		nuclia_log( 'type : '.$post->post_type );
		nuclia_log( 'password protected: '.( $post->post_password ? 'yes' : 'no' ) );
		nuclia_log( 'status : '.$post->post_status );
		
		// indexable post type
		if ( !array_key_exists( $post->post_type, $this->get_indexable_post_types() ) ) return;
		
		nuclia_log( 'post type is indexable' );
		
		// resource id if already indexed
		$rid = get_post_meta( $post_id, 'nuclia_rid', true );
			
		// do not index or delete, if not public or password protected
		$dont_index = ( $post->post_password || $post->post_status !== 'publish' ) ? true : false;

		
		if ( $rid ) { // post already indexed
			nuclia_log( 'post already indexed' );
			$action = $dont_index ? 'delete_resource' : 'modify_resource';
		} else { // post not indexed
			nuclia_log( 'post not indexed' );
			$action = $dont_index ? 'none' : 'create_resource';
		};
		
		nuclia_log( 'action : '.$action );	
		
		switch ( $action ) {
			case 'create_resource' :
				$body = $this->prepare_nuclia_resource_body( $post );
				$this->api->create_resource( $post_id, $body );
				break;
			case 'modify_resource' :
				$body = $this->prepare_nuclia_resource_body( $post );
				$this->api->modify_resource( $post_id, $rid, $body );
				break;
			case 'delete_resource' :
				$this->api->delete_resource( $post_id, $rid );
				break;
			default :
				return;
				
		}
	}

	
	/**
	 * Prepare NucliaDB resource body
	 *
	 * @param WP_Post $post Post to index into NucliaDb.
	 *
	 * @return array  Prepared resource body array
	 */
	
	public function prepare_nuclia_resource_body( WP_Post $post ) {
		$body = array(
			'title' => html_entity_decode( wp_strip_all_tags( $post->post_title ), ENT_QUOTES, "UTF-8" ),
			'slug' => (string)$post->ID,
			'metadata' => array(
				'language' => get_bloginfo("language")
			)
		);
		
		// for attachments
		if ( $post->post_type == 'attachment' ) :
			$body = array_merge( $body, array(
				'icon' => get_post_mime_type( $post->ID ),
				'links' => array( 
					'file--1' => array(
						'uri' => wp_get_attachment_url( $post->ID ),
					)
				)
			));
			
		// other post types
		else :
			$body = array_merge( $body, array(
				'icon' => 'text/html',
				'origin' => array(
					'url' => get_permalink( $post ),
				),
				'texts' => array( 
					'text-1' => array(
						'body' => apply_filters('the_content', $post->post_content ),
						'format' => 'HTML',
					)
				)
			));
			
		endif;
		
		return json_encode( $body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	}
	 
}

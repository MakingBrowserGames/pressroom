<?php
/**
 * Plugin Name: Pressroom Pro
 * Plugin URI:
 * Description: PressRoom turns Wordpress into a multi channel publishing environment.
 * Version: 1.0
 * Author: ThePrintLabs
 * Author URI: http://www.theprintlabs.com
 * License: GPLv2
 *
 *   _____                                               _____
 *  |  __ \                                             |  __ \
 *  | |__) | __ ___  ___ ___ _ __ ___   ___  _ __ ___   | |__) | __ ___
 *  |  ___/ '__/ _ \/ __/ __| '__/ _ \ / _ \| '_ ` _ \  |  ___/ '__/ _ \
 *  | |   | | |  __/\__ \__ \ | | (_) | (_) | | | | | | | |   | | | (_) |
 *  |_|   |_|  \___||___/___/_|  \___/ \___/|_| |_| |_| |_|   |_|  \___/
 *
 *  Copyright © 2014 - thePrintLabs Ltd.
 */

if (!defined( 'ABSPATH' )) exit; // Exit if accessed directly

require_once( __DIR__ . '/core/define.php' );
require_once( __DIR__ . '/core/settings.php' );
require_once( PR_LIBS_PR_PATH . 'utils.php' );

require_once( PR_CORE_PATH . 'setup.php' );
require_once( PR_CORE_PATH . 'edition/edition.php' );
require_once( PR_CORE_PATH . 'edition/editorial_project.php' );
require_once( PR_CORE_PATH . 'posts.php' );
require_once( PR_CORE_PATH . 'theme.php' );
require_once( PR_CORE_PATH . 'packager/packager.php' );
require_once( PR_CORE_PATH . 'preview/preview.php' );
require_once( PR_CORE_PATH . 'api.php' );

require_once( PR_CONFIGS_PATH . 'edd.php' );
require_once( PR_CONFIGS_PATH . 'tgm.php' );

require_once( PR_SERVER_PATH . 'server.php' );

require_once( PR_LIBS_PR_PATH . 'UI/metabox.php' );
require_once( PR_LIBS_PR_PATH . 'UI/press_list.php' );

class TPL_Pressroom
{
	public $configs;
	public $edition;
	public $preview;

	public function __construct() {

		if ( !is_admin() ) {
			return;
		}

		$this->_load_configs();
		$this->_load_pages();
		$this->_load_extensions();

		$this->_create_edition();
		$this->_create_preview();


		register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
		add_action( 'admin_notices', array( $this, 'check_pressroom_notice' ), 20 );
		add_action( 'p2p_init', array( $this, 'register_post_connection' ) );
		add_filter( 'p2p_created_connection', array( $this, 'post_connection_add_default_theme' ) );
		add_filter( 'theme_root', array( $this, 'set_theme_root' ), 10 );
	}

	/**
	 * Activation plugin:
	 * Setup database tables and filesystem structure
	 *
	 * @void
	 */
	public function plugin_activation() {

		$errors = PR_Setup::install();
		if ($errors !== false) {
			$html = '<h1>' . __('Pressroom') . '</h1>
			<p><b>' .__( 'An error occurred during activation. Please see details below.', 'pressroom_setup' ). '</b></p>
			<ul><li>' .implode( "</li><li>", $errors ). '</li></ul>';
			wp_die( $html, __( 'Pressroom activation error', 'pressroom_setup' ), ('back_link=true') );
		}

		do_action( 'press_flush_rules' );
		flush_rewrite_rules();
	}

	/**
	 * Deactivation plugin
	 *
	 * @void
	 */
	public function plugin_deactivation() {

		// delete_option('rewrite_rules');
		flush_rewrite_rules();

	}

	/**
	 * Add connection between
	 * the edition and other allowed post type.
	 *
	 * @void
	 */
	public function register_post_connection() {

		$types = $this->get_allowed_post_types();

		p2p_register_connection_type( array(
				'name' 		=> P2P_EDITION_CONNECTION,
				'from'	 	=> $types,
				'to' 			=> PR_EDITION,
				'sortable' 	=> false,
				'title' => array(
    				'from'	=> __( 'Included into edition', 'pressroom' )
    			),
				'to_labels' => array(
      			'singular_name'	=> __( 'Edition', 'pressroom' ),
      			'search_items' 	=> __( 'Search edition', 'pressroom' ),
      			'not_found'			=> __( 'No editions found.', 'pressroom' ),
      			'create'				=> __( 'Select an edition', 'pressroom' ),
  				),
				'admin_box' => array(
					'show' 		=> 'from',
					'context'	=> 'side',
					'priority'	=> 'high',
				),
				'fields' => array(
					'status' => array(
						'title'		=> __( 'Included', 'pressroom' ),
						'type'		=> 'checkbox',
						'default'	=> 1,
					),
					'template' => array(
						'title' 		=> '',
						'type' 		=> 'hidden',
						'values'		=>	array(),
					),
					'order' => array(
						'title'		=> '',
						'type' 		=> 'hidden',
						'default' 	=> 0,
						'values' 	=>	array(),
					),
				)
		) );
	}

	/**
	 * Add default theme template to post connection
	 *
	 * @param  int $p2p_id
	 * @void
	 */
	public function post_connection_add_default_theme( $p2p_id ) {

		$connection = p2p_get_connection( $p2p_id );
		if ( $connection->p2p_type == P2P_EDITION_CONNECTION ) {
			$themes = PR_Theme::get_themes();
			$theme_code = get_post_meta( $connection->p2p_to, '_pr_theme_select', true );
			if ( $theme_code && $themes ) {
				$pages = $themes[$theme_code];
				foreach ( $pages as $page ) {
					if ( $page['rule'] == 'post' ) {
						p2p_add_meta( $p2p_id, 'template', $page['filename'] );
					}
				}
			}
		}
	}

	/**
	 * Check admin notices and display
	 *
	 * @echo
	 */
	public function check_pressroom_notice() {

		if ( isset( $_GET['pmtype'] ) && isset( $_GET['pmcode'] ) ) {

			$msg_type = $_GET['pmtype'];
			$msg_code = $_GET['pmcode'];
			$msg_param = isset( $_GET['pmparam'] ) ? urldecode( $_GET['pmparam'] ) : '';

			echo '<div class="' . $msg_type . '"><p>';
			switch ( $msg_code ) {
				case 'theme':
					echo _e( '<b>Error:</b> You must specify a theme for edition!', 'pressroom_notice' );
					break;
				case 'duplicate_entry':
					echo _e( sprintf('<b>Error:</b> Duplicate entry for <b>%s</b>. It must be unique', $msg_param ) );
					break;
				case 'failed_activated_license':
					echo _e( sprintf('<b>Error during activation:</b> %s', $msg_param ) );
					break;
				case 'success_activated_license':
					echo _e( sprintf('<b>Activation successful:</b> %s', $msg_param ) );
					break;
				case 'failed_deactivated_license':
					echo _e( sprintf('<b>Error during deactivation:</b> %s', $msg_param ) );
					break;
				case 'success_deactivated_license':
					echo _e( '<b>License Deactivated.</b>' );
					break;
			}
			echo '</p></div>';
		}
	}

	/**
   * Unset theme root to exclude custom filter override
   *
   * @param string $path
   * return string;
   */
  public function set_theme_root( $path ) {

    if ( isset( $_GET['pr_no_theme'] ) ) {
      return PR_THEMES_PATH;
    }

    return $path;
  }

	/*
	 * Get all allowed post types
	 *
	 * @return array
	 */
	public function get_allowed_post_types() {

		$types = array( 'post', 'page' );
		$custom_types = $this->_load_custom_post_types();
		$types = array_merge( $types, $custom_types );

		return $types;
	}

	/**
	 * Check if is add or edit page
	 *
	 * @param  string  $new_edit
	 * @return boolean
	 */
	public static function is_edit_page() {

		global $pagenow;
    	if ( !is_admin() ) {
			return false;
		}

		return in_array( $pagenow, array( 'post.php' ) );
	}

	/**
	 * Load plugin configuration settings
	 *
	 * @void
	 */
	protected function _load_configs() {

		if ( is_null( $this->configs ) ) {
			$this->configs = get_option('pr_settings', array(
				'pr_custom_post_type' => array()
			));
		}
	}

	/**
	 * Load plugin extensions
	 *
	 * @void
	 */
	protected function _load_extensions() {

		if ( is_dir( PR_EXTENSIONS_PATH ) ) {
			$files = PR_Utils::search_files( PR_EXTENSIONS_PATH, 'php' );
			if ( !empty( $files ) ) {
				foreach ( $files as $file ) {
					require_once( $file );
				}
			}
		}
	}

	/**
	* Load plugin pages
	*
	* @void
	*/
	protected function _load_pages() {

		if ( is_dir( PR_PAGES_PATH ) ) {
			$files = PR_Utils::search_files( PR_PAGES_PATH, 'php' );
			if ( !empty( $files ) ) {
				foreach ( $files as $file ) {
					require_once( $file );
				}
			}
		}
	}

	/**
	 * Load custom post types configured in settings page
	 *
	 * @return array - custom post types
	 */
	protected function _load_custom_post_types() {

		$types = array();
		if ( !empty( $this->configs ) && isset( $this->configs['pr_custom_post_type'] ) ) {
			if ( is_array( $this->configs['pr_custom_post_type'] ) ) {
				foreach ( $this->configs['pr_custom_post_type'] as $post_type ) {
					array_push( $types, $post_type );
				}
			}
			else {
				array_push( $types, $this->configs['pr_custom_post_type'] );
			}
		}
		return $types;
	}

	/**
	* Instance a new edition object
	*
	* @void
	*/
	protected function _create_edition() {

		if ( is_null( $this->edition ) ) {
			$this->edition = new PR_Edition;
		}
	}

	/**
	* Instance a new preview object
	*
	* @void
	*/
	protected function _create_preview() {

		if ( is_null( $this->preview ) ) {
			$this->preview = new PR_Preview;
		}
	}
}

/* instantiate the plugin class */
$tpl_pressroom = new TPL_Pressroom();

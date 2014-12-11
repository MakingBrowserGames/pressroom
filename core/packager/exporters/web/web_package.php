<?php
/**
* PressRoom packager: Web package
*/
require_once( PR_PACKAGER_CONNECTORS_PATH . '/ftp_sftp.php' );

final class PR_Packager_Web_Package
{

  public $package_settings = array();

  public function __construct() {

    add_action( 'pr_add_eproject_tab', array( $this, 'pr_add_option' ), 10, 2 );
    add_action( 'pr_add_edition_tab', array( $this, 'pr_add_option' ), 10, 2 );
    add_action( 'wp_ajax_test_ftp_connection', array( $this, 'test_ftp_connection' ) );

    add_action( 'pr_packager_web_start', array( $this, 'web_packager_start' ), 10, 2 );
    add_action( 'pr_packager_web', array( $this, 'web_packager_run' ), 10, 4 );
    add_action( 'pr_packager_web_end', array( $this, 'web_packager_end' ), 10, 2 );
  }

  /**
   * Check edition post settings else check for editorial project settings
   *
   * @param  int $edition_id
   * @param  int $eproject_id
   * @void
   */
  public function load_settings( $edition_id, $eproject_id ) {

    $settings = array(
      '_pr_container_theme',
      '_pr_ftp_protocol',
      '_pr_ftp_server',
      '_pr_ftp_user',
      '_pr_ftp_password',
      '_pr_ftp_destination_path'
    );

    if( !$settings ) {
      return false;
    }

    $override = get_post_meta( $edition_id, '_pr_web_override_eproject', true );

    foreach( $settings as $setting ) {

      if( $override ) {
        $option = get_post_meta( $edition_id, $setting, true );
      }
      else if( $eproject_id ) {
        $option = PR_Editorial_Project::get_config( $eproject_id, $setting);
      }
      if( $option ) {
        $this->package_settings[$setting] = $option;
      }
    }
  }

  /**
   * Create toc and load settings.
   *
   * @param  object $packager
   * @param  object $editorial_project
   * @void
   */
  public function web_packager_start( $packager, $editorial_project ) {

    $packager->make_toc( $editorial_project );
    $this->load_settings( $packager->edition_post->ID, $editorial_project->term_id );

  }

  /**
   * Rewrite urls from html string and save file.
   *
   * @param  object $packager
   * @param  object $post
   * @param  object $editorial_project
   * @param  string $parsed_html_post
   * @void
   */
  public function web_packager_run( $packager, $post, $editorial_project, $parsed_html_post ) {

    // Rewrite post url
    $parsed_html_post = $packager->rewrite_url( $parsed_html_post );

    do_action( 'pr_packager_run_web_' . $post->post_type, $post, $packager->edition_dir );

    if ( !$packager->save_html_file( $parsed_html_post, $post->post_title, $packager->edition_dir ) ) {
      PR_Packager::print_line( __( 'Failed to save post file: ', 'packager' ) . $post->post_title, 'error' );
      continue;
    }
  }

  /**
   * Save attachments, set package date and close the package.
   *
   * @param  object $packager
   * @param  object $editorial_project
   * @void
   */
  public function web_packager_end( $packager, $editorial_project ) {

    $media_dir = PR_Utils::make_dir( $packager->edition_dir, PR_EDITION_MEDIA );
    if ( !$media_dir ) {
      PR_Packager::print_line( __( 'Failed to create folder ', 'web_package' ) . $packager->edition_dir . DIRECTORY_SEPARATOR . PR_EDITION_MEDIA, 'error' );
      $packager->exit_on_error();
      return;
    }
    $packager->set_progress( 70, __( 'Saving edition attachments files', 'web_package' ) );

    $packager->save_posts_attachments( $media_dir );
    $packager->set_progress( 78, __( 'Saving edition cover image', 'web_package' ) );

    $packager->save_cover_image();
    $packager->set_progress( 80, __( 'Generating book json', 'web_package' ) );

    $packager->set_package_date();
    $packager->set_progress( 90, __( 'Generating web package', 'web_package' ) );

    $this->_web_write( $packager, $editorial_project );

  }

  /**
   * Create metabox and custom fields
   *
   * @param object &$metaboxes
   * @param int $item_id (it can be editorial project id or edition id);
   */
  public function pr_add_option( &$metaboxes, $item_id ) {

    global $post;

    $web = new PR_Metabox( 'web_metabox', __( 'web', 'web_package' ), 'normal', 'high', $item_id );

    if( $post ) {
      $web->add_field( '_pr_web_override_eproject', __( 'Override Editorial Project settings', 'editorial_project' ), __( 'If enabled, will be used edition settings below', 'edition' ), 'checkbox', false );
    }

    $web->add_field( '_pr_container_theme', __( 'Container theme', 'web_package' ), __( 'Web viewer theme', 'web_package' ), 'select', '', array(
      'options' => array(
        array( 'value' => 'standard', 'text' => __( "Standard Web Viewer", 'web_package' ) ),
        array( 'value' => 'no-container', 'text' => __( "No container", 'web_package' ) ),
        )
      )
    );

    do_action_ref_array( 'pr_add_web_field', array( &$web ) );

    array_push( $metaboxes, $web );
  }

  /**
   * test ftp connection
   *
   * @void
   */
  public function test_ftp_connection() {

    $server = isset( $_POST['server'] ) ? $_POST['server'] : false ;
    $port = isset( $_POST['port'] ) ? $_POST['port'] : false ;
    $base = isset( $_POST['base'] ) ? $_POST['base'] : false ;
    $username = isset( $_POST['user'] ) ? $_POST['user'] : false ;
    $password = isset( $_POST['password'] ) ? $_POST['password'] : false ;
    $protocol = isset( $_POST['protocol'] ) ? $_POST['protocol'] : false ;

    $params = array(
      "hostname"  => $server,
      "base"      => $base,
      "port"      => (int) $port,
      "username"  => $username,
      "password"  => $password,
      "protocol"  => $protocol,
    );

    $ftp = new PR_Ftp_Sftp();

    if( $ftp->connect( $params ) ) {
      wp_send_json_success( array( 'message'=> 'Connection successfully', 'class'=>'success' ) );
      exit;
    }
    else {
      wp_send_json_error( array( 'message'=> $ftp->errors->get_error_messages(), 'class'=>'failure' ) );
      exit;
    }

    exit;
  }

  /**
   * Check transfer protocol and transfer files
   *
   * @param  object $editorial_project
   * @void
   */
  protected function _web_write( $packager, $editorial_project ) {

    switch( $this->package_settings['_pr_ftp_protocol'] ) {
      case 'local':
        $package_name = PR_Utils::sanitize_string ( $editorial_project->slug ) . '_' . $packager->edition_post->ID;
        PR_Utils::recursive_copy( $packager->edition_dir, PR_WEB_PATH . $package_name );
        $filename = PR_WEB_PATH . $package_name . '.zip';
        $cover_post = $packager->linked_query->posts[0];
        $cover = PR_Utils::sanitize_string($cover_post->post_title) . '.html';
        if ( PR_Utils::create_zip_file( $packager->edition_dir, $filename, '' ) ) {
          PR_Packager::print_line( __( 'Package created. You can see it <a href="'. PR_WEB_URI . $package_name . DIRECTORY_SEPARATOR . $cover .'">there</a> or <a href="'. PR_WEB_URI . $package_name . '.zip">download</a>', 'web_package' ), 'success' );
        }
        break;
      case 'ftp':
      case 'sftp':
        $ftp = new PR_Ftp_Sftp();

        var_dump($this->package_settings);
        $params = array(
          "hostname"  => isset( $this->package_settings['_pr_ftp_server'][0] ) ? $this->package_settings['_pr_ftp_server'][0] : '',
          "base"      => isset( $this->package_settings['_pr_ftp_destination_path'] ) ? $this->package_settings['_pr_ftp_destination_path'] : '',
          "port"      => isset( $this->package_settings['_pr_ftp_server'][1] ) ? (int) $this->package_settings['_pr_ftp_server'][1] : '',
          "username"  => isset( $this->package_settings['_pr_ftp_user'] ) ? $this->package_settings['_pr_ftp_user'] : '',
          "password"  => isset( $this->package_settings['_pr_ftp_password'] ) ? $this->package_settings['_pr_ftp_password'] : '',
          "protocol"  => isset( $this->package_settings['_pr_ftp_protocol'] ) ? $this->package_settings['_pr_ftp_protocol'] : '',
        );

        if( $ftp->connect( $params ) ) {
          PR_Packager::print_line( __( 'Ftp connection successfull  ', 'web_package' ) , 'success' );
          PR_Packager::print_line( __( 'Start transfer', 'web_package' ) , 'success' );
          if( $ftp->recursive_copy( $packager->edition_dir, $this->package_settings['_pr_ftp_destination_path'] ) ) {
            PR_Packager::print_line( __( 'Transfer complete', 'web_package' ), 'success' );
          }
          else {
            PR_Packager::print_line( __( 'Error during transfer', 'web_package' ), 'error' );
          }
        }
        else {
          $error = $ftp->errors->get_error_message('connect');
          PR_Packager::print_line( __( 'Failed to connect. More details: ', 'web_package' ) . ( is_array( $error) ? $error[0] : $error ) , 'error' );
          $packager->exit_on_error();
          exit;

        }

        break;
    }
  }
}
$pr_packager_web_package = new PR_Packager_Web_Package;

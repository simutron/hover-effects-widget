<?php
/*
Plugin Name: Hover Effects Widget
Plugin URI: https://www.simutron.de/
Description: Create a widget, which supports CSS3 based effects on hover.
Version: 1.0.0
Author: simutron IT-Service
Author URI: https://www.simutron.de

Hover Effects Widget is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.
 
Hover Effects Widget is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Hover Effects Widget. If not, see {URI to Plugin License}.
*/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if( !defined( 'HEW_VER' ) )
	define( 'HEW_VER', '1.0.0' );

  // Start up the engine
class Hover_Effects_Widget_Plugin
{
	/**
	 * Static property to hold our singleton instance
	 *
	 */
  static $instance = false;
  
	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	private function __construct() {
    
    // Constants
    add_action ( 'plugins_loaded', array($this, 'constants'), 2);

    // Includes
    add_action ( 'plugins_loaded', array( $this, 'includes'), 3);

    // I18N
		add_action( 'plugins_loaded', array( $this, 'load_i18n'), 4);

    // Register widget
    add_action( 'widgets_init', array( $this, 'register_widgets'));

    add_action( 'wp_enqueue_scripts', array( $this, 'load_public_assets' ) );

		add_action( 'load-widgets.php', array( $this, 'load_admin_assets' ) );
		add_action( 'load-customize.php', array( $this, 'load_admin_assets' ) );
		add_action( 'widgets_admin_page', array( $this, 'output_wp_editor_widget_html' ), 100 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'output_wp_editor_widget_html' ), 1 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'customize_controls_print_footer_scripts' ), 2 );
		//add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

    // Filter
    add_filter( 'wp_editor_widget_content', 'wptexturize' );
		add_filter( 'wp_editor_widget_content', 'convert_smilies' );
		add_filter( 'wp_editor_widget_content', 'convert_chars' );
		add_filter( 'wp_editor_widget_content', 'wpautop' );
		add_filter( 'wp_editor_widget_content', 'shortcode_unautop' );
		add_filter( 'wp_editor_widget_content', 'do_shortcode', 11 );
  }
  
	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return Hover_Effects_Widget_Plugin
	 */
	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
  } // END getInstance()
  
	/**
	 * load textdomain
	 *
	 * @return void
	 */
	public function load_i18n() {
		load_plugin_textdomain( 'hew', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
  }
  
  /**
	 * constants
   * 
   * @return void
	 */
	function constants() {

		define( 'HEW_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'HEW_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
	}

  /**
   * includes
   * 
   * @return void
   */
  function includes() {
    require_once( HEW_DIR . 'includes/class-hover-effects-widget.php' );
  }

  /**
   * register_widgets
   * 
   * @return void
   */
  public function register_widgets() {
    register_widget( 'Hover_Effects_Widget' );
  }

  /**
	 * output_wp_editor_widget_html
	 * 
   * @return void
	 */
	public function output_wp_editor_widget_html() {
		
		?>
		<div id="wp-editor-widget-container" style="display: none;">
			<a class="close" href="javascript:WPEditorWidget.hideEditor();" title="<?php esc_attr_e( 'Close', 'wp-editor-widget' ); ?>"><span class="icon"></span></a>
			<div class="editor">
				<?php
				$settings = array(
					'textarea_rows' => 20,
				);
				wp_editor( '', 'wpeditorwidget', $settings );
				?>
				<p>
					<a href="javascript:WPEditorWidget.updateWidgetAndCloseEditor(true);" class="button button-primary"><?php _e( 'Save and close', 'wp-editor-widget' ); ?></a>
				</p>
			</div>
		</div>
		<div id="wp-editor-widget-backdrop" style="display: none;"></div>
		<?php
		
	} // END output_wp_editor_widget_html()
	
	/**
	 * customize_controls_print_footer_scripts
   * 
   * @return void
	 */
	public function customize_controls_print_footer_scripts() {
	
		// Because of https://core.trac.wordpress.org/ticket/27853
		// Which was fixed in 3.9.1 so we only need this on earlier versions
		$wp_version = get_bloginfo( 'version' );
		if ( version_compare( $wp_version, '3.9.1', '<' ) && class_exists( '_WP_Editors' ) ) {
			_WP_Editors::enqueue_scripts();
		}
		
	} // END customize_controls_print_footer_scripts

  /**
	 * load_admin_assets
	 * 
   * @return void
	 */
	public function load_admin_assets() {

		wp_register_script( 'wp-editor-widget-js', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), HEW_VER );
		wp_enqueue_script( 'wp-editor-widget-js' );

		wp_register_style( 'wp-editor-widget-css', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), HEW_VER );
		wp_enqueue_style( 'wp-editor-widget-css' );

	} // END load_admin_assets()

  /**
   * load_public_assets
   * 
   * @return void
   */
  public function load_public_assets() {
		wp_register_style( 'hew-css', plugins_url( 'assets/css/hew.css', __FILE__ ), array(), HEW_VER );
		wp_enqueue_style( 'hew-css' );
  } // END load_public_assets()

} // END Hover_Effects_Widget_Plugin

// Instantiate our class
$Hover_Effects_Widget_Plugin = Hover_Effects_Widget_Plugin::getInstance();
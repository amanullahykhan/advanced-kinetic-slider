<?php
/**
 * Plugin Name: Advanced Kinetic Slider for Elementor
 * Description: Commercial-grade Elementor slider with Ken Burns backgrounds, video backgrounds, 12 kinetic content animations, autoplay progress bar, and full accessibility support.
 * Version: 2.0.0
 * Author: Amanullah Khan
 * Text Domain: adv-kinetic-slider
 * Requires Plugins: elementor
 * Elementor tested up to: 3.26
 * Elementor Pro tested up to: 3.26
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ADV_KINETIC_SLIDER_VERSION', '2.0.0' );
define( 'ADV_KINETIC_SLIDER_FILE', __FILE__ );
define( 'ADV_KINETIC_SLIDER_PATH', plugin_dir_path( __FILE__ ) );
define( 'ADV_KINETIC_SLIDER_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main plugin bootstrap class.
 * Wrapping everything in a class + checking for Elementor before doing
 * anything else prevents fatal errors on sites where Elementor is
 * deactivated, disabled, or not yet loaded (a common cause of the
 * "white screen of death" with widget-only plugins built as loose functions).
 */
final class Advanced_Kinetic_Slider_Plugin {

	const MINIMUM_ELEMENTOR_VERSION = '3.5.0';
	const MINIMUM_PHP_VERSION       = '7.4';

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
	}

	public function on_plugins_loaded() {
		if ( ! $this->is_compatible() ) {
			return;
		}
		add_action( 'elementor/widgets/register', [ $this, 'register_widget' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'register_assets' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_assets' ] );
	}

	private function is_compatible() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_missing_elementor' ] );
			return false;
		}

		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_minimum_elementor_version' ] );
			return false;
		}

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_minimum_php_version' ] );
			return false;
		}

		return true;
	}

	/**
	 * Registered on both frontend and editor enqueue hooks. wp_register_*
	 * only registers the handle — it does not print it to the page.
	 * Elementor pulls it in when the widget's get_script_depends() /
	 * get_style_depends() are read, so double-registration here is safe
	 * and just guarantees the handle exists in every context Elementor
	 * might need it (editor preview iframe included).
	 */
	public function register_assets() {
		if ( ! wp_style_is( 'advanced-slider-css', 'registered' ) ) {
			wp_register_style(
				'advanced-slider-css',
				ADV_KINETIC_SLIDER_URL . 'assets/css/advanced-slider.css',
				[],
				ADV_KINETIC_SLIDER_VERSION
			);
		}

		if ( ! wp_script_is( 'advanced-slider-js', 'registered' ) ) {
			wp_register_script(
				'advanced-slider-js',
				ADV_KINETIC_SLIDER_URL . 'assets/js/advanced-slider.js',
				[ 'jquery', 'elementor-frontend' ],
				ADV_KINETIC_SLIDER_VERSION,
				true
			);
		}
	}

	public function register_widget( $widgets_manager ) {
		require_once ADV_KINETIC_SLIDER_PATH . 'widgets/widget-advanced-slider.php';
		$widgets_manager->register( new \Elementor_Advanced_Slider_Widget() );
	}

	public function notice_missing_elementor() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'adv-kinetic-slider' ),
			'<strong>' . esc_html__( 'Advanced Kinetic Slider', 'adv-kinetic-slider' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'adv-kinetic-slider' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'adv-kinetic-slider' ),
			'<strong>' . esc_html__( 'Advanced Kinetic Slider', 'adv-kinetic-slider' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'adv-kinetic-slider' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'adv-kinetic-slider' ),
			'<strong>' . esc_html__( 'Advanced Kinetic Slider', 'adv-kinetic-slider' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'adv-kinetic-slider' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
}

Advanced_Kinetic_Slider_Plugin::instance();

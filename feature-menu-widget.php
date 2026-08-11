<?php
/**
 * Plugin Name: Feature Menu Widget for Elementor
 * Description: Apple-style smooth feature menu (hover image/video switching, submenus, single links) as an editable Elementor widget.
 * Version: 1.0.0
 * Author: Krevix
 * Text Domain: feature-menu-widget
 * Requires PHP: 7.4
 * Elementor tested up to: 3.26
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'FMW_VERSION', '1.1.6' );
define( 'FMW_PATH', plugin_dir_path( __FILE__ ) );
define( 'FMW_URL', plugin_dir_url( __FILE__ ) );

final class Feature_Menu_Widget_Plugin {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
	}

	public function on_plugins_loaded() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );
			return;
		}

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
	}

	public function admin_notice_missing_elementor() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Feature Menu Widget requires Elementor to be installed and activated.', 'feature-menu-widget' ) .
			'</p></div>';
	}

	public function register_category( $elements_manager ) {
		$elements_manager->add_category( 'feature-menu', [
			'title' => __( 'Feature Menu', 'feature-menu-widget' ),
			'icon'  => 'eicon-nav-menu',
		] );
	}

	public function register_styles() {
		wp_register_style( 'fmw-style', FMW_URL . 'assets/css/feature-menu.css', [], FMW_VERSION );
		wp_register_style( 'afmw-style', FMW_URL . 'assets/css/accordion-feature-menu.css', [], FMW_VERSION );
	}

	public function register_scripts() {
		wp_register_script( 'fmw-gsap', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js', [], '3.12.5', true );
		wp_register_script( 'fmw-script', FMW_URL . 'assets/js/feature-menu.js', [ 'jquery', 'fmw-gsap' ], FMW_VERSION, true );
		wp_register_script( 'afmw-script', FMW_URL . 'assets/js/accordion-feature-menu.js', [ 'jquery', 'fmw-gsap' ], FMW_VERSION, true );
	}

	public function register_widgets( $widgets_manager ) {
		require_once FMW_PATH . 'widgets/class-feature-menu-widget.php';
		require_once FMW_PATH . 'widgets/class-accordion-feature-menu-widget.php';
		$widgets_manager->register( new \Feature_Menu_Widget() );
		$widgets_manager->register( new \Accordion_Feature_Menu_Widget() );
	}
}

Feature_Menu_Widget_Plugin::instance();

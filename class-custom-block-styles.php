<?php
/**
 * Custom Block Styles Class
 *
 * A reusable class to register custom block styles with associated stylesheets.
 * Simplifies the process of adding block style variations by automating
 * stylesheet registration and block style registration.
 *
 * @package Custom_Block_Styles
 * @version 1.0.0
 * @author  Your Name
 * @license GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Custom_Block_Styles
 *
 * Handles registration of custom block styles and their associated stylesheets.
 *
 * @since 1.0.0
 */
class Custom_Block_Styles {

	/**
	 * Array of block styles to register
	 *
	 * @var array
	 */
	private $block_styles;

	/**
	 * Base path for style files relative to theme directory
	 *
	 * @var string
	 */
	private $styles_path;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param array  $block_styles Array of block style configurations.
	 *                             Each item should have 'block', 'name', and 'label' keys.
	 * @param string $styles_path  Base path for CSS files relative to theme directory.
	 *                             Default: '/assets/css/styles/'.
	 */
	public function __construct( $block_styles, $styles_path = '/assets/css/styles/' ) {
		$this->block_styles = $block_styles;
		$this->styles_path  = trailingslashit( $styles_path );

		// Hook into WordPress
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'init', array( $this, 'register_block_styles' ) );
	}

	/**
	 * Enqueue all block style stylesheets
	 *
	 * Automatically registers and enqueues CSS files based on the block style name.
	 * CSS filename should match the 'name' value with .css extension.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		foreach ( $this->block_styles as $style ) {
			if ( ! isset( $style['name'] ) ) {
				continue;
			}

			$handle   = $style['name'];
			$css_file = $this->styles_path . $handle . '.css';
			$css_path = get_template_directory() . $css_file;

			// Only register if the file exists
			if ( ! file_exists( $css_path ) ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( sprintf( 'Custom Block Styles: CSS file not found: %s', $css_path ) );
				continue;
			}

			wp_register_style(
				$handle,
				get_template_directory_uri() . $css_file,
				array(),
				filemtime( $css_path )
			);

			wp_enqueue_style( $handle );
		}
	}

	/**
	 * Register all block styles
	 *
	 * Registers block style variations with WordPress.
	 * Uses the 'name' value as the style handle.
	 *
	 * @since 1.0.0
	 */
	public function register_block_styles() {
		foreach ( $this->block_styles as $style ) {
			// Validate required keys
			if ( ! isset( $style['block'], $style['name'], $style['label'] ) ) {
				continue;
			}

			register_block_style(
				$style['block'],
				array(
					'name'         => $style['name'],
					'label'        => $style['label'],
					'style_handle' => $style['name'],
				)
			);
		}
	}
}


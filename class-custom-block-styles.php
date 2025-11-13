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
	 * Stylesheet dependencies
	 *
	 * @var array
	 */
	private $dependencies;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param array  $block_styles Array of block style configurations.
	 *                             Each item should have 'block', 'name', and 'label' keys.
	 * @param string $styles_path  Base path for CSS files relative to theme directory.
	 *                             Default: '/assets/css/styles/'.
	 * @param array  $dependencies Array of stylesheet handles to depend on.
	 *                             Default: array().
	 */
	public function __construct( $block_styles, $styles_path = '/assets/css/styles/', $dependencies = array() ) {
		$this->block_styles  = $block_styles;
		$this->styles_path   = trailingslashit( $styles_path );
		$this->dependencies  = $dependencies;

		// Hook into WordPress
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_styles' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_dependencies' ) );
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
				$this->dependencies,
				filemtime( $css_path )
			);

			wp_enqueue_style( $handle );
		}
	}

	/**
	 * Enqueue stylesheet dependencies in the block editor
	 *
	 * Ensures dependencies are available in the editor so block styles
	 * can load correctly. If a dependency is not registered, attempts to
	 * register it using common theme file paths.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_editor_dependencies() {
		if ( empty( $this->dependencies ) ) {
			return;
		}

		foreach ( $this->dependencies as $dependency ) {
			// If dependency is not registered, try to register it
			if ( ! wp_style_is( $dependency, 'registered' ) ) {
				// Common theme style patterns
				$possible_paths = array(
					'style.css',
					'assets/css/style.css',
					get_stylesheet() . '.css',
				);

				foreach ( $possible_paths as $path ) {
					$file_path = get_template_directory() . '/' . $path;
					if ( file_exists( $file_path ) ) {
						wp_register_style(
							$dependency,
							get_template_directory_uri() . '/' . $path,
							array(),
							filemtime( $file_path )
						);
						break;
					}
				}
			}

			// Enqueue if registered (either already or just registered above)
			if ( wp_style_is( $dependency, 'registered' ) && ! wp_style_is( $dependency, 'enqueued' ) ) {
				wp_enqueue_style( $dependency );
			}
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

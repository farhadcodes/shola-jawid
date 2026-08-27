<?php
/**
 * Plugin Name: Shola Core
 * Plugin URI: https://github.com/farhadcodes/shola-jawid
 * Description: Content model for شعله جاوید (Shola Jawid) — custom post types, taxonomies, and meta fields. Companion plugin to the shola-jawid theme; content survives a theme switch.
 * Version: 1.0.1
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Farhad Farhaad
 * Author URI: https://github.com/farhadcodes
 * License: GNU General Public License v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: shola-core
 *
 * @package SholaCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SHCORE_VERSION', '1.0.1' );
define( 'SHCORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'SHCORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * PSR-4-style autoloader for the SholaCore\ namespace, mapping to includes/
 * using WP's own class-file naming convention (class-{kebab-case}.php).
 * No Composer dependency needed at this project's size.
 *
 * @param string $class_name Fully-qualified class name being loaded.
 * @return void
 */
function shcore_autoload( $class_name ) {
	$prefix = 'SholaCore\\';

	if ( 0 !== strpos( $class_name, $prefix ) ) {
		return;
	}

	$relative = substr( $class_name, strlen( $prefix ) );
	$file     = SHCORE_PATH . 'includes/class-' . strtolower( str_replace( '_', '-', $relative ) ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
}
spl_autoload_register( 'shcore_autoload' );

\SholaCore\Post_Types::init();
\SholaCore\Taxonomies::init();
\SholaCore\Meta_Fields::init();
\SholaCore\Contact_Settings::init();
\SholaCore\Social_Links_Settings::init();
\SholaCore\Label_Settings::init();
\SholaCore\View_Counter::init();
\SholaCore\Roles::init();
\SholaCore\SEO::init();
\SholaCore\Security::init();

/**
 * On activation: register post types and taxonomies, seed the fixed term
 * vocabularies (idempotent — safe on repeat activation), then flush
 * rewrite rules once so the new permalink structures take effect
 * immediately without a manual visit to Settings → Permalinks.
 *
 * @return void
 */
function shcore_activate() {
	\SholaCore\Post_Types::register_post_types();
	\SholaCore\Post_Types::register_rewrite_tags();
	\SholaCore\Taxonomies::register_taxonomies();
	\SholaCore\Taxonomies::register_topic_rewrite();
	\SholaCore\Taxonomies::create_default_terms();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'shcore_activate' );

/**
 * On deactivation: flush rewrite rules so the custom permalink structures
 * don't linger after the plugin (and its post types) are gone.
 *
 * @return void
 */
function shcore_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'shcore_deactivate' );

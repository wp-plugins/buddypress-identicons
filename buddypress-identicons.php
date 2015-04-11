<?php
/**
 * Plugin Name: BuddyPress Identicons
 * Plugin URI: https://github.com/henrywright/buddypress-identicons
 * Description: GitHub-style identicons for your BuddyPress site.
 * Version: 1.1.3
 * Author: Henry Wright
 * Author URI: http://about.me/henrywright
 * Text Domain: buddypress-identicons
 * Domain Path: /languages/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * BuddyPress Identicons
 *
 * @package BuddyPress Identicons
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Include the plugin's files.
 *
 * @since 1.1.0
 */
function bi_init() {

	/**
	 * Require the plugin's classes.
	 */
	require dirname( __FILE__ ) . '/inc/classes.php';

	/**
	 * Require the plugin's functions.
	 */
	require dirname( __FILE__ ) . '/inc/functions.php';

	/**
	 * Require the plugin's admin.
	 */
	require dirname( __FILE__ ) . '/inc/admin.php';
}
add_action( 'bp_include', 'bi_init' );

/**
 * Load the plugin's textdomain.
 * 
 * @since 1.1.0
 */
function bi_i18n() {

	load_plugin_textdomain( 'buddypress-identicons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'bi_i18n' );

/**
 * Check if BuddyPress is active.
 *
 * @since 1.1.0
 *
 * @return bool True if BuddyPress is active, false if not.
 */
function bi_buddypress_exists() {

	if ( class_exists( 'BuddyPress' ) ) {
		return true;
	} else {
		return false;
	}
}
add_action( 'plugins_loaded', 'bi_buddypress_exists' );

/**
 * Output an admin notice if BuddyPress isn't active.
 *
 * @since 1.1.0
 */
function bi_admin_notice() {

	if ( ! bi_buddypress_exists() ) {
		?>
		<div class="error">
			<p><?php _e( 'BuddyPress Identicons requires BuddyPress version 1.7 or higher.', 'buddypress-identicons' ); ?></p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'bi_admin_notice' );

/**
 * Check if the plugin is network activated.
 *
 * @since 1.1.0
 *
 * @return bool True if network activated, else false.
 */
function bi_is_network_active() {
	if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Plugin activation tasks.
 *
 * @since 1.1.0
 */
function bi_plugin_activate() {

	// Set the avatar default.
	update_blog_option( get_current_blog_id(), 'avatar_default', plugins_url( 'images/pixicon.png', __FILE__ ) );
}
register_activation_hook( __FILE__, 'bi_plugin_activate' );

/**
 * Plugin deactivation tasks.
 *
 * @since 1.1.0
 */
function bi_plugin_deactivate() {

	// Reset the avatar default.
	if ( bi_usage_check() ) {
		update_blog_option( get_current_blog_id(), 'avatar_default', 'mystery' );
	}
}
register_deactivation_hook( __FILE__, 'bi_plugin_deactivate' );
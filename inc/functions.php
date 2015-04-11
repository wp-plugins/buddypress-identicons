<?php
/**
 * User defined functions
 *
 * @package BuddyPress Identicons
 * @subpackage Functions
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Set up constants.
 *
 * @since 1.1.1
 */
function bi_constants() {

	if ( ! defined( 'IDENTICON_WIDTH' ) ) {
		define( 'IDENTICON_WIDTH', 120 );
	}

	if ( ! defined( 'IDENTICON_HEIGHT' ) ) {
		define( 'IDENTICON_HEIGHT', 120 );
	}
}
add_action( 'bp_init', 'bi_constants', 3 );

/**
 * Disable Gravatar.
 *
 * @since 1.1.0
 *
 * @return bool True to disable Gravatar, false to not.
 */
function bi_no_grav() {

	if ( bi_usage_check() ) {
		return true;
	} else {
		return false;
	}
}
add_filter( 'bp_core_fetch_avatar_no_grav', 'bi_no_grav' );

/**
 * Filter the avatar defaults.
 *
 * @since 1.1.0
 *
 * @param array $avatar_defaults An array of avatar defaults.
 * @return array An array of avatar defaults.
 */
function bi_avatar_defaults( $avatar_defaults ) {

	$pixicon = plugins_url( 'images/pixicon.png', dirname(__FILE__) );

	$avatar_defaults[$pixicon] = __( 'Pixicon' );

	return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'bi_avatar_defaults' );

/**
 * Filter the default avatar URL.
 *
 * @since 1.1.0
 *
 * @param string $bp_core_avatar_default The URL of the default avatar.
 * @param array $params See {@see bp_core_fetch_avatar()}.
 * @return string The URL of the identicon.
 */
function bi_default_avatar_url( $bp_core_avatar_default, $params ) {

	// Bail if identicons aren't in use.
	if ( ! bi_usage_check() ) {
		return $bp_core_avatar_default;
	}

	$user = get_userdata( $params['item_id'] );

	// Bail if the user doesn't exist.
	if ( ! $user )
		return plugins_url( 'images/pixicon.png', dirname(__FILE__) );

	$identicon = Identicon_Factory::spawn( $params['item_id'] );

	$identicon->create();

	$bp_core_avatar_default = $identicon->read();

	return $bp_core_avatar_default;
}
add_filter( 'bp_core_default_avatar_user', 'bi_default_avatar_url', 10, 2 );

/**
 * Delete an identicon.
 *
 * @since 1.1.0
 *
 * @param int $user_id The ID of the identicon owner.
 */
function bi_delete( $user_id ) {

	$identicon = Identicon_Factory::spawn( $user_id );

	$identicon->delete();
}
add_action( 'delete_user', 'bi_delete' );
add_action( 'bp_core_pre_delete_account', 'bi_delete' );

/**
 * Check if identicons are in use.
 *
 * @since 1.1.0
 *
 * @return bool True if identicons are being used, else false.
 */
function bi_usage_check() {

	$avatar_default = get_blog_option( get_current_blog_id(), 'avatar_default' );

	$array = array();

	$array[] = plugins_url( 'images/pixicon.png', dirname(__FILE__) );

	if ( in_array( $avatar_default, $array ) ) {
		return true;
	} else {
		return false;
	}
}
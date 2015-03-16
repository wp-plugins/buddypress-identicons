<?php
/**
 * Admin functions
 *
 * @package BuddyPress Identicons
 * @subpackage Admin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Register the admin settings.
 *
 * @since 1.1.0
 */
function bi_register_admin_settings() {

	add_settings_section(
		'identicons',
		__( 'Identicons', 'buddypress-identicons' ),
		'bi_settings_section_callback',
		'buddypress'
	);

	add_settings_field(
		'bi-background',
		__( 'Background', 'buddypress-identicons' ),
		'bi_settings_field_callback_background',
		'buddypress',
		'identicons'
	);

	add_settings_field(
		'bi-padding',
		__( 'Padding', 'buddypress-identicons' ),
		'bi_settings_field_callback_padding',
		'buddypress',
		'identicons'
	);

	register_setting(
		'buddypress',
		'bi-background',
		'intval'
	);

	register_setting(
		'buddypress',
		'bi-padding',
		'intval'
	);
}
add_action( 'bp_register_admin_settings', 'bi_register_admin_settings', 99 );

/**
 * Fill the section with content.
 *
 * @since 1.1.0
 */
function bi_settings_section_callback() {}

/**
 * Add an input to the field.
 *
 * @since 1.1.0
 */
function bi_settings_field_callback_background() {

	$value = get_blog_option( get_current_blog_id(), 'bi-background' );
	?>
	<input type="checkbox" name="bi-background" id="bi-background" value="1" <?php checked( $value ); ?> />
	<label for="bi-background"><?php _e( 'Set background to transparent', 'buddypress-identicons' ); ?></label>
	<?php
}

/**
 * Add an input to the field.
 *
 * @since 1.1.0
 */
function bi_settings_field_callback_padding() {

	$value = get_blog_option( get_current_blog_id(), 'bi-padding', 1 );
	?>
	<input type="checkbox" name="bi-padding" id="bi-padding" value="1" <?php checked( $value ); ?> />
	<label for="bi-padding"><?php _e( 'Add padding to the image', 'buddypress-identicons' ); ?></label>
	<?php
}
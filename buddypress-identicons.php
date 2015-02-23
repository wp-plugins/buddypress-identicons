<?php
/**
 * Plugin Name: BuddyPress Identicons
 * Plugin URI: https://github.com/henrywright/buddypress-identicons
 * Description: GitHub-style identicons for your BuddyPress site.
 * Version: 1.0.2
 * Author: Henry Wright
 * Author URI: http://about.me/henrywright
 * Text Domain: buddypress-identicons
 * Domain Path: /languages/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The Identicon class definition.
 *
 * @since 1.0.0
 * @access public
 */
final class Identicon {

	/**
	 * The relative path to the plugin's directory (without trailing slash).
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $plugin_dir;

	/**
	 * Info on the uploads directory.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $upload_dir;

	/**
	 * The class instance.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object
	 */
	private static $instance;

	/**
	 * __construct() method.
	 *
	 * Sets variables and adds the actions and filters necessary for the plugin to function.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function __construct() {

		$this->plugin_dir = dirname( plugin_basename( __FILE__ ) );

		$this->upload_dir = wp_upload_dir();

		// add_action( 'plugins_loaded', array( $this, 'i18n' ) );

		add_action( 'delete_user', array( $this, 'delete' ) );

		add_action( 'bp_core_deleted_account', array( $this, 'delete' ) );

		add_filter( 'bp_core_fetch_avatar_no_grav', array( $this, 'no_grav' ) );

		add_filter( 'bp_core_default_avatar_user', array( $this, 'default_avatar_url' ), 10, 2 );
	}

	/**
	 * Loads the plugin's textdomain.
	 *
	 * This function is not currently used because there's no text to localise. The plugin is entirely image-based.
	 * 
	 * @since 1.0.0
	 * @access public
	 */
	function i18n() {
		load_plugin_textdomain( 'buddypress-identicons', false, $this->plugin_dir . '/languages' );
	}

	/**
	 * Creates an identicon.
	 *
	 * Creates a multidimensional array of boolean values and uses them to determine if each square in a 5 by 5 grid is painted.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param int $user_id The ID of the identicon owner.
	 */
	function create( $user_id ) {

		// Bail if an identicon already exists for this user.
		if ( self::has_identicon( $user_id ) )
			return;

		// Get user info.
		$user = get_userdata( $user_id );

		// Calculate the MD5 hash of the user's username.
		$hash = md5( $user->user_login );

		// Get the first 6 characters of the hash to make the hex triplet.
		$hex_triplet = substr( $hash, 0, 6 );

		$pixels = array();

		for ( $a = 0; $a < 5; $a++ ) {
			for ( $b = 0; $b < 5; $b++ ) {

				// Get the hexadecimal string.
				$hex = substr( $hash, ( $a * 5 ) + $b + 6, 1 );

				// Convert the hexadecimal string to decimal.
				$dec = hexdec( $hex );

				// Add true or false to the array.
				$pixels[$a][$b] = $dec % 2 === 0;
			}
		}

		// Create a new image 360px by 360px.
		$image = imagecreatetruecolor( 360, 360 );

		// Break the hex triplet into red, green and blue parts.
		$r = substr( $hex_triplet, 0, 2 );
		$g = substr( $hex_triplet, 2, 2 );
		$b = substr( $hex_triplet, 4, 2 );

		// Allocate the pixel colour.
		$pixel = imagecolorallocate( $image, '0x' . $r, '0x' . $g, '0x' . $b );

		// Allocate the background colour.
		$background = imagecolorallocate( $image, '0xee', '0xee', '0xee' );

		// Fill the image with a background colour.
		imagefill( $image, 0, 0, $background );

		// Paint columns 1 to 3.
		for ( $x = 0; $x < 3; $x++ ) {
			for ( $y = 0; $y < 5; $y++ ) {
				$colour = $background;

				if ( $pixels[$x][$y] )
					$colour = $pixel;

				// Set the point coordinates.
				$x1 = 30 + ( $x * 60 );
				$y1 = 30 + ( $y * 60 );
				$x2 = 30 + ( ( $x + 1 ) * 60 );
				$y2 = 30 + ( ( $y + 1 ) * 60 );

				imagefilledrectangle( $image, $x1, $y1, $x2, $y2, $colour );
			}
		}

		// Paint column 4. To achieve symmetry, column 4 is the same as column 2.
		for ( $x = 3; $x < 4; $x++ ) {
			for ( $y = 0; $y < 5; $y++ ) {
				$colour = $background;

				if ( $pixels[$x - 2][$y] )
					$colour = $pixel;

				// Set the point coordinates.
				$x1 = 30 + ( $x * 60 );
				$y1 = 30 + ( $y * 60 );
				$x2 = 30 + ( ( $x + 1 ) * 60 );
				$y2 = 30 + ( ( $y + 1 ) * 60 );

				imagefilledrectangle( $image, $x1, $y1, $x2, $y2, $colour );
			}
		}

		// Paint column 5. To achieve symmetry, column 5 is the same as column 1.
		for ( $x = 4; $x < 5; $x++ ) {
			for ( $y = 0; $y < 5; $y++ ) {
				$colour = $background;

				if ( $pixels[$x - 4][$y] )
					$colour = $pixel;

				// Set the point coordinates.
				$x1 = 30 + ( $x * 60 );
				$y1 = 30 + ( $y * 60 );
				$x2 = 30 + ( ( $x + 1 ) * 60 );
				$y2 = 30 + ( ( $y + 1 ) * 60 );

				imagefilledrectangle( $image, $x1, $y1, $x2, $y2, $colour );
			}
		}

		// Make a directory inside the uploads directory if it doesn't exist.
		wp_mkdir_p( $this->upload_dir['basedir'] . '/identicons' );

		// Save the image.
		$path = $this->upload_dir['basedir'] . '/identicons/' . $user->user_login . '.png';
		imagepng( $image, $path );

	}

	/**
	 * Deletes an identicon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $user_id The ID of the identicon owner.
	 */
	function delete( $user_id ) {

		// Get user info.
		$user = get_userdata( $user_id );

		$dir_path = $this->upload_dir['basedir'] . '/identicons';

		// Bail if the directory doesn't exist.
		if ( ! file_exists( $dir_path ) )
			return false;

		// Bail if the file doesn't exist.
		if ( ! file_exists( $dir_path . '/' . $user->user_login . '.png' ) )
			return false;

		// Delete the file.
		@unlink( $dir_path . '/' . $user->user_login . '.png' );

		// Remove the directory if it's empty.
		@rmdir( $dir_path );
	}

	/**
	 * Checks if a given user has an identicon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $user_id The ID of the user to check.
	 * @return bool
	 */
	function has_identicon( $user_id ) {

		// Get user info.
		$user = get_userdata( $user_id );

		$dir_path = $this->upload_dir['basedir'] . '/identicons';

		if ( ! file_exists( $dir_path . '/' . $user->user_login . '.png' ) )
			return false;
		else
			return true;
	}

	/**
	 * Disables Gravatar.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	function no_grav() {
		return true;
	}

	/**
	 * Filters the default avatar URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $bp_core_avatar_default The URL of the default avatar.
	 * @param array $params An array of variables such as width, height and alt.
	 * @return string
	 */
	function default_avatar_url( $bp_core_avatar_default, $params ) {

		if ( ! self::has_identicon( $params['item_id'] ) ) {

			// Create an identicon for this user.
			self::create( $params['item_id'] );
		}

		// Get user info.
		$user = get_userdata( $params['item_id'] );

		// Point to the identicon.
		$bp_core_avatar_default = $this->upload_dir['baseurl'] . '/identicons/' . $user->user_login . '.png';

		return $bp_core_avatar_default;
	}

	/**
	 * Returns the instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return object
	 */
	static function get_instance() {
		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

}

Identicon::get_instance();
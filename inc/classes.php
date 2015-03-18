<?php
/**
 * Class definitions
 *
 * @package BuddyPress Identicons
 * @subpackage Classes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * A class definition detailing an identicon.
 *
 * @since 1.0.0
 * @access public
 */
abstract class Identicon {

	/**
	 * The name of the parent directory.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	const DIR = 'identicons';

	/**
	 * Info on the identicon owner.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var object
	 */
	protected $user;

	/**
	 * An image resource identifier.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var resource
	 */
	protected $image;

	/**
	 * Info on the uploads directory.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var array
	 */
	protected $upload_dir;

	/**
	 * The file extension of the image.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var string
	 */
	private $ext = '.png';

	/**
	 * Set up necessary values.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $user_id The ID of the identicon owner.
	 */
	function __construct( $user_id ) {

		$this->user = get_userdata( $user_id );

		$this->upload_dir = wp_upload_dir();

		if ( is_ssl() ) {
			$this->upload_dir['baseurl'] = str_replace( 'http://', 'https://', $this->upload_dir['baseurl'] );
		}

		$this->create_dir();
	}

	/**
	 * Create directories.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	function create_dir() {

		// Create directory if it doesn't exist.
		if ( ! file_exists( trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . $this->user->ID ) ) {

			if ( ! wp_mkdir_p( trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . $this->user->ID ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Create an identicon.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	abstract function create();

	/**
	 * Get an identicon's URL.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return string|bool The URL of the identicon or false if the identicon doesn't exist.
	 */
	function read() {

		if ( $this->identicon_exists() ) {
			return trailingslashit( $this->upload_dir['baseurl'] ) . trailingslashit( self::DIR ) . trailingslashit( $this->user->ID ) . $this->type . '-' . $this->user->user_login . $this->ext;
		} else {
			return false;
		}
	}

	/**
	 * Output the image to file.
	 *
	 * @since 1.1.0
	 * @access protected
	 */
	protected function save() {

		imagepng( $this->image, trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . trailingslashit( $this->user->ID ) . $this->type . '-' . $this->user->user_login . $this->ext );
	}

	/**
	 * Delete an identicon.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function delete() {

		if ( $this->identicon_exists() ) {
			@unlink( trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . trailingslashit( $this->user->ID ) . $this->type . '-' . $this->user->user_login . $this->ext );
		}
	}

	/**
	 * Check if an identicon exists for a given user.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return bool True if an identicon exists or false if it doesn't.
	 */
	function identicon_exists() {

		if ( file_exists( trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . trailingslashit( $this->user->ID ) . $this->type . '-' . $this->user->user_login . $this->ext ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Destructor.
	 *
	 * Destroy stuff to free memory.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	function __destruct() {

		if ( is_resource( $this->image ) ) {
			imagedestroy( $this->image );
		}
	}
}

/**
 * A class definition detailing a Pixicon.
 *
 * @since 1.1.0
 * @access public
 */
final class Pixicon extends Identicon {

	/**
	 * The type of identicon.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var string
	 */
	protected $type = 'pixicon';

	/**
	 * Set up necessary values.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param int $user_id The ID of the identicon owner.
	 */
	function __construct( $user_id ) {

		parent::__construct( $user_id );
	}

	/**
	 * Create an identicon.
	 *
	 * Build an array of boolean values and use that data to determine if each square in a 5 x 5 grid should be painted.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	function create() {

		// Bail if an identicon exists.
		if ( $this->identicon_exists() ) {
			return;
		}

		// Calculate the hash of user_login.
		$hash = md5( $this->user->user_login );

		// Build an array of boolean data from the hash, a 32-character hexadecimal number.
		$data = array();

		for ( $x = 0; $x < 5; $x++ ) {

			for ( $y = 0; $y < 5; $y++ ) {

				$data[$x][$y] = hexdec( substr( $hash, ( $x * 5 ) + $y + 6, 1 ) ) % 2 === 0;
			}
		}

		$this->image = imagecreatetruecolor( BP_AVATAR_FULL_WIDTH, BP_AVATAR_FULL_HEIGHT );

		// Set a grey background.
		$background = imagecolorallocate( $this->image, '0xee', '0xee', '0xee' );

		if ( get_blog_option( get_current_blog_id(), 'bi-background' ) == 1 ) {

			// Set a transparent background.
			imagecolortransparent( $this->image, $background );
		}

		// Extract 6 digits to serve as a hex triplet.
		$ht = substr( $hash, 0, 6 );

		// Break into red, green and blue parts.
		$r = substr( $ht, 0, 2 );
		$g = substr( $ht, 2, 2 );
		$b = substr( $ht, 4, 2 );

		$foreground = imagecolorallocate( $this->image, '0x' . $r, '0x' . $g, '0x' . $b );

		imagefill( $this->image, 0, 0, $background );

		// Set the unit values.
		$x_unit = BP_AVATAR_FULL_WIDTH / 5;
		$y_unit = BP_AVATAR_FULL_HEIGHT / 5;

		// Set the padding.
		$x_pad = 0;
		$y_pad = 0;

		if ( get_blog_option( get_current_blog_id(), 'bi-padding' ) == 1 ) {

			$x_unit = BP_AVATAR_FULL_WIDTH / 6;
			$y_unit = BP_AVATAR_FULL_HEIGHT / 6;

			$x_pad = $x_unit / 2;
			$y_pad = $y_unit / 2;
		}

		for ( $x = 0; $x < 5; $x++ ) {

			for ( $y = 0; $y < 5; $y++ ) {

				$colour = $background;

				switch ( $x ) {
					case 3:
						// To achieve symmetry, make column 4 the same as column 2.
						$z = 1;
						break;
					case 4:
						// To achieve symmetry, make column 5 the same as column 1.
						$z = 0;
						break;
					default:
						$z = $x;
				}

				if ( $data[$z][$y] ) {
					$colour = $foreground;
				}

				// Set the coordinates.
				$x1 = $x_pad + ( $x * $x_unit );
				$y1 = $y_pad + ( $y * $y_unit );
				$x2 = $x_pad + ( ( $x + 1 ) * $x_unit );
				$y2 = $y_pad + ( ( $y + 1 ) * $y_unit );

				imagefilledrectangle( $this->image, $x1, $y1, $x2, $y2, $colour );
			}
		}

		$this->save();
	}
}

/**
 * A class to create objects.
 *
 * @since 1.1.0
 * @access public
 */
abstract class Identicon_Factory {

	/**
	 * A method to create objects.
	 *
	 * The value of the avatar_default option determines the object to be created.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string|int $user_id The ID of the identicon owner.
	 * @return object
	 */
	static function spawn( $user_id ) {

		$avatar_default = get_blog_option( get_current_blog_id(), 'avatar_default' );

		$pixicon = plugins_url( 'images/pixicon.png', dirname(__FILE__) );

		switch( $avatar_default ) {
			case $pixicon:
				return new Pixicon( $user_id );
				break;
		}
	}
}
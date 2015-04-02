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
	 * The width of the image.
	 *
	 * @since 1.1.1
	 * @access protected
	 * @var int
	 */
	protected $width;

	/**
	 * The height of the image.
	 *
	 * @since 1.1.1
	 * @access protected
	 * @var int
	 */
	protected $height;

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

		$this->width = (int) IDENTICON_WIDTH;

		$this->height = (int) IDENTICON_HEIGHT;

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

			// Delete the file.
			@unlink( trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . trailingslashit( $this->user->ID ) . $this->type . '-' . $this->user->user_login . $this->ext );

			// Remove the directory, if empty.
			@rmdir( trailingslashit( $this->upload_dir['basedir'] ) . trailingslashit( self::DIR ) . $this->user->ID );
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
class Pixicon extends Identicon {

	/**
	 * The type of identicon.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var string
	 */
	protected $type = 'pixicon';

	/**
	 * Set up.
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
	 * Create a Pixicon.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	function create() {

		// Bail if an identicon exists.
		if ( $this->identicon_exists() ) {
			return;
		}

		$str = $this->user->user_login;

		$hash = md5( $str );

		$data = array();

		for ( $x = 0; $x < 5; $x++ ) {

			for ( $y = 0; $y < 5; $y++ ) {

				$hex = substr( $hash, ( $x * 5 ) + $y + 6, 1 );

				$dec = hexdec( $hex );

				$data[$x][$y] = $dec % 2 === 0;
			}
		}

		$unit_w = $this->width / 6;

		$unit_h = $this->height / 6;

		$padding_h = $unit_w / 2;

		$padding_v = $unit_h / 2;

		$this->image = imagecreatetruecolor( $this->width, $this->height );

		$temp = imagecreatetruecolor( $this->width - ( $padding_h * 2 ), $this->height - ( $padding_v * 2 ) );

		$r = substr( $hash, 0, 2 );

		$g = substr( $hash, 2, 2 );

		$b = substr( $hash, 4, 2 );

		$foreground = imagecolorallocate( $this->image, '0x' . $r, '0x' . $g, '0x' . $b );

		$background = imagecolorallocate( $this->image, '0xee', '0xee', '0xee' );

		if ( get_blog_option( get_current_blog_id(), 'bi-background' ) == 1 ) {

			imagecolortransparent( $this->image, $background );

			imagecolortransparent( $temp, $background );
		}

		imagefill( $this->image, 0, 0, $background );

		for ( $x = 0; $x < 5; $x++ ) {

			for ( $y = 0; $y < 5; $y++ ) {

				switch ( $x ) {
					case 3:
						$shift = 2;
						break;
					case 4:
						$shift = 4;
						break;
					default:
						$shift = 0;
				}

				$color = $background;

				if ( $data[$x - $shift][$y] ) {
					$color = $foreground;
				}

				$x1 = $x * $unit_w;

				$y1 = $y * $unit_h;

				$x2 = ( $x + 1 ) * $unit_w;

				$y2 = ( $y + 1 ) * $unit_h;

				imagefilledrectangle( $temp, $x1, $y1, $x2, $y2, $color );
			}
		}

		$dst_im = $this->image;

		$src_im = $temp;

		$dst_x = $padding_h;

		$dst_y = $padding_v;

		$src_x = 0;

		$src_y = 0;

		$src_w = $this->width - ( $padding_h * 2 );

		$src_h = $this->height - ( $padding_v * 2 );

		imagecopy( $dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h );

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
	 * Use the value of avatar_default to determine which object is created.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param int $user_id The ID of the identicon owner.
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
=== BuddyPress Identicons ===
Contributors: henry.wright
Donate link: https://www.bhf.org.uk/get-involved/donate
Tags: buddypress, identicons, avatars
Requires at least: 3.2
Tested up to: 4.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Github-style identicons for your BuddyPress site.

== Description ==

This BuddyPress plugin automatically replaces default avatars with identicons. An identicon is a visual representation of a hash value generated from a member's username. The approach taken ensures a huge number of unique identicons are available.

The original identicon idea was published in 2007 by [Don Park](https://github.com/donpark/identicon). That implementation used a 9-block graphic and was written in Java. The inspiration for this plugin came from the implementation developed in 2013 by [GitHub](https://github.com/blog/1586-identicons). Their identicons are simple 5 x 5 pixel sprites that are generated using a hash of the user's ID.

== Installation ==

1. Download, install and activate the plugin.
1. Enjoy!

== Frequently Asked Questions ==

= Why is my identicon different to GitHub's version even though my username is the same? =

The logic used to generate the identicon in GitHub's implementation is most probably different to that used here.

= I don't like my identicon. Can I change it? =

Unfortunately, no.

= Can I remove the identicon's border? =

Yes. To remove the border, paste the following into your functions.php file:

`add_action( 'wp_print_styles', 'bp_identicons_deregister_style', 100 );

function bp_identicons_deregister_style() {
	wp_deregister_style( 'buddypress-identicons' );
}`

= Only some members have an identicon. Why is that? =

Identicons are used only if a member hasn't uploaded a profile photo, in place of the default avatar. All new members will be allocated an identicon. Members who registered prior to the date of plugin activation will need to log in to get their identicon.

= Where should I submit bug reports? =

If you think you've spotted a bug, please let me know by opening an issue on the plugin's [GitHub](https://github.com/henrywright/buddypress-identicons) repo.

== Screenshots ==

1. This identicon belongs to _henrywright_
2. This identicon belongs to _mitch_
3. This identicon belongs to _lorrainewright_

== Changelog ==

= 1.0.0 =
* Initial release.
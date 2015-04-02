=== BuddyPress Identicons ===
Contributors: henry.wright
Donate link: https://www.bhf.org.uk/get-involved/donate
Tags: buddypress, bbpress, identicons, avatars, users
Requires at least: 3.2
Tested up to: 4.1.1
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

GitHub-style identicons for your BuddyPress site.

== Description ==

By default, members that haven't uploaded a profile photo are given a mystery-man avatar. This BuddyPress plugin automatically replaces default avatars with GitHub-style identicons. Each member's identicon is highly likely to be unique because it's generated from a hash of their username.

The original identicon idea was published in 2007 by Don Park. A 9-block graphic was used and the project was written in the Java programming language. See his [Identicon](https://github.com/donpark/identicon) repository on GitHub for more information. The inspiration for this plugin came from the implementation developed by [GitHub](https://github.com/blog/1586-identicons) in 2013. Their identicons are simple 5 x 5 'pixel' sprites that are generated using a hash of the user's ID.

== Installation ==

1. Download, install and activate the plugin.
1. Enjoy!

== Frequently Asked Questions ==

= Is the plugin compatible with bbPress? =

Yes.

= Is the plugin network compatible? =

Yes.

= Why is my identicon different to GitHub's version even though my username is the same? =

The logic used to generate the identicon in GitHub's implementation is most probably different to that used here.

= I don't like my own identicon. Can I change it? =

Unfortunately, no.

= Can I make the image background transparent? =

Yes. To set a transparent background go to Settings > BuddyPress and then look for the option under the Settings tab.

= Can I change the image size? =

Yes. The image size is determined by constants. The default is 120px square but you can change that by defining the constants in your bp-custom.php file. For example:

`define ( 'IDENTICON_WIDTH', 180 );
define ( 'IDENTICON_HEIGHT', 180 );`

= Can I change the identicon type? =

Yes. To change the identicon type go to Settings > Discussion and select an option under Default Avatar.

= Why is it that some members don't have an identicon? =

An identicon is used as a member's avatar only if a profile photo hasn't been uploaded.

= Where should I submit bug reports? =

If you think you've spotted a bug, please let me know by opening an issue on the [BuddyPress Identicons](https://github.com/henrywright/buddypress-identicons) GitHub repo.

== Screenshots ==

1. This identicon belongs to henrywright
2. This identicon belongs to mitch
3. This identicon belongs to lorrainewright

== Changelog ==

= 1.1.2 =
* Fixed errors associated with file removal on user account deletion.

= 1.1.1 =
* Removed padding option.
* Decreased default image width and height.
* Minor bug fixes.

= 1.1.0 =
* Refactored code.
* Made network compatible.
* Added transparent background option.
* Added padding option.
* Added ability to change width and height.
* Bug fixes.

= 1.0.2 =
* Do image generation when `bp_core_fetch_avatar()` is called.

= 1.0.1 =
* Removed the need for a CSS border.

= 1.0.0 =
* Initial release.
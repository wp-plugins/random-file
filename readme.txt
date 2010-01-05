=== Random File ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: random, file, coffee2code
Requires at least: 1.5
Tested up to: 2.9.1
Stable tag: 1.5.2
Version: 1.5.2

Retrieve the name, path, or link to a randomly chosen file in a specified directory.

== Description ==

Retrieve the name, path, or link to a randomly chosen file in a specified directory.

Useful for displaying random images/logos or including text from random files onto your site (writing excerpts, multi-line quotes, etc).

Notes:

* If you want to actually display the name of the random file, be sure to 'echo' the results:
`<?php echo c2c_random_file('/random'); ?>`

* Unless you limit the file search to only include a particular extension (via `$extensions` argument), all files in the specified `$dir` will be under consideration for random selection

* Can be run inside or outside of "the loop"

== Installation ==

1. Unzip `random-file.zip` inside the `/wp-content/plugins/` directory (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Make use of the `c2c_random_file()` template function in your template (see examples below).

== Template Tags ==

The plugin provides one optional template tag for use in your theme templates.

= Functions =

* `<?php function c2c_random_file( $dir, $extensions='', $reftype='relative', $exclusions='' ) ?>`
This retrieves the name of a random file from a specified directory and returns information based on the file according to the `$reftype` value.

= Arguments =

* `$dir`
The directory to search for a random file.  The directory must exist at the directory structure level of your WordPress installation or below. (i.e., if your site is installed on your server at `/usr/local/htdocs/yoursite/www/journal/`, then the directory of random files you specified will assume that as its base... so a value of `'randomfiles'` would be assumed to actually be: `/usr/local/htdocs/yoursite/www/journal/randomfiles/`)

* `$extensions`
Optional argument.  A space-separated list of extensions (case insensitive), i.e. 'jpg gif png jpeg'.

* `$reftype`
Optional argument.  Can be one of the following: 'relative' (which is the default), 'absolute', 'url', 'filename', 'hyperlink'.  See Examples section for more details and examples.

* `$exclusions`
Optional argument.  If specified, MUST be an array of filenames to exclude from consideration as a random file.

= Examples =

* The reference to the randomly selected file can be returned in one of five ways:
[Assume your WordPress installation is at http://www.yoursite.org/journal/ and you've invoked `c2c_random_file('random/', 'txt', $reftype)`]

	$reftype = 'relative'
	=> A location relative to the primary domain:
	/journal/random/randomfile.txt
	[This is the default setting as it is the most applicable.  Relative referencing is necessary if
	the random file is to be used as an argument to include() or virtual().  It's also a valid way
	to reference a file for A HREF= and IMG SRC= linking.]

	$reftype = 'absolute'
	=> An absolute location relative to the root of the server's file system:
	/usr/local/htdocs/yoursite/www/journal/random/randomfile.txt

	$reftype = 'url'
	=> The URL of the random file:
	http://www.yoursite.org/journal/random/randomfile.txt
	[If you desire the use of full URL, such as for a A HREF= or IMG SRC= link.]

	$reftype = 'filename'
	=> The filename of the random file:
	randomefile.txt
	
	$reftype = 'hyperlink'
	=> The filename of the random file hyperlinked to that random file:
	<a href='http://yoursite.org/journal/random/randomfile.txt' title='randomfile.txt'>randomfile.txt</a>

* Include random logo or image on your site:
`<img alt="logo" class="logo" src="<?php echo c2c_random_file('/wp-content/images/logos/'); ?>" />`

* Insert text from a random file (i.e. for random multi-line quotes) (Apache web-server only, probably):

`<blockquote class='todayquote'>
   <?php virtual(c2c_random_file('/quotes/', 'txt')); ?>
</blockquote>`

* If you wanted to source a random .php file
`<?php include(c2c_random_file('/randomphp', 'php')); ?>`

== Frequently Asked Questions ==

= Does this plugin do dynamic random rotation within a loaded page (i.e. randomly rotating images within a loaded page)? =

No.  This plugin only selects a random file when the page is loaded.  Once loaded, it does not currently add any dynamic functionality to automatically retrieve another random file on its own.

== Changelog ==

= 1.5.2 =
* Add PHPDoc documentation
* Note compatibility with WP 2.9+
* Update copyright date
* Update readme.txt

= 1.5.1 =
* Fixed: missing '/' in path construction for reftype 'absolute'

= 1.5 =
* Added new reftype of 'hyperlink' to return the filename of the random file hyperlinked to that file
* Added error checking to avoid error when referenced directory does not exist
* Added error checking for when there is an error opening a directory
* Explicit handling of reftype 'absolute' in the code was actually supposed to be 'serverabsolute'
* Minor code tweaks
* Tweaked installation instructions
* Added readme.txt
* Noted and tested compatibility with WP 2.3.3 through 2.8

= 1.0 =
* Renamed function from random_file() to c2c_random_file()
* Added new reftype of 'filename'
* Added optional array argument $exceptions for files not to be considered in random file selection
* Updated license and examples
* Verified that plugin works in WP 1.5 (and still works in WP 1.2)

= 0.9 =
* Initial release
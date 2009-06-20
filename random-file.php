<?php
/*
Plugin Name: Random File
Version: 1.5
Plugin URI: http://coffee2code.com/wp-plugins/random-file
Author: Scott Reilly
Author URI: http://coffee2code.com
Description: Retrieve the name, path, or link to a randomly chosen file in a specified directory.

Useful for displaying random images/logos or including text from random files onto your site (writing excerpts, multi-line quotes, etc).

Compatible with WordPress 1.5+, 2.0+, 2.1+, 2.2+, 2.3+, 2.5+, 2.6+, 2.7+, 2.8+.

=>> Read the accompanying readme.txt file for more information.  Also, visit the plugin's homepage
=>> for more information and the latest updates

Installation:

1. Download the file http://coffee2code.com/wp-plugins/random-file.zip and unzip it into your
/wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' admin menu in WordPress
3. Make use of the c2c_random_file() template function in your template (see examples below).


Notes:

- If you want to actually display the name of the random file, be sure to 'echo' the results:
<?php echo c2c_random_file('/random'); ?>

- The directory of random files must exist at the directory structure level of your WordPress
installation or below. (i.e., if your site is installed on your server at
/usr/local/htdocs/yoursite/www/journal/, then the directory of random files you specified will assume
that as its base... so $dir='randomfiles' would be assumed to actually be:
/usr/local/htdocs/yoursite/www/journal/randomfiles/)

- Leading and trailing '/' are unnecessary... '/randomfiles/' == '/randomfiles' == 'randomfiles/' == 'randomfiles'

- $extensions can be a space-separated list of extensions (case insensitive), i.e. 'jpg gif png jpeg'

- Unless you limit the file search to only include a particular extension (via $extensions argument), all files in
the specified $dir will be under consideration for random selection

- The reference to the randomly selected file can be returned in one of five ways:
[Assume your WordPress installation is at http://www.yoursite.org/journal/ and you've
invoked c2c_random_file('random/', 'txt', $reftype)]

	$reftype = 'relative'
	=> A location relative to the primary domain:
	/journal/random/randomfile.txt
	[This is the default setting as it is the most applicable.  Absolute referencing is necessary if
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

- The $exclusions argument, if specified, MUST be an array of filenames to exclude from consideration as a random file

- Can be run inside or outside of "the loop."

Examples:

// Include random logo or image on your site:
<img alt="logo" class="logo" src="<?php echo c2c_random_file('/wp-content/images/logos/'); ?>" />

// Insert text from a random file (i.e. for random multi-line quotes) (Apache web-server only, probably):
<blockquote class='todayquote'>
   <?php virtual(c2c_random_file('/quotes/', 'txt')); ?>
</blockquote>

// If you wanted to source a random .php file
<?php include(c2c_random_file('/randomphp', 'php')); ?>

*/

/*
Copyright (c) 2004-2009 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

function c2c_random_file( $dir, $extensions='', $reftype='relative', $exclusions='' ) {
	$files = array();
	$i = -1;
	$pattern = '/.*';
	if ( !empty($extensions) ) $pattern .= '\.(' . implode('|', explode(' ', $extensions)) . ')';
	$pattern .= '$/i';
	$dir = trim($dir, '/');
	$abs_dir = ABSPATH . $dir;
	if ( !file_exists($abs_dir) )
		return;
	$handle = @opendir($abs_dir);
	if ( false === $handle )
		return;
	$exclusions = empty($exclusions) ? array() : array_map('basename', $exclusions);
	while ( false != ($file = readdir($handle)) ) {
		if ( is_file($abs_dir . '/' . $file) && preg_match($pattern, $file) && !in_array($file, $exclusions) ) {
			$files[] = $file;
			++$i;
		}
	}
	closedir($handle);
	if ( empty($files) ) return;
	
	mt_srand((double)microtime()*1000000);
	$rand = mt_rand(0, $i);
	if ( !empty($dir) ) $dir .= '/';
	$random_file = $files[$rand];
	if ( 'url' == $reftype ) {
		return get_settings('siteurl') . '/' . $dir . $random_file;
	} elseif ( 'absolute' == $reftype ) {
		return $abs_dir . $random_file;	/* could also do realpath($random_file); */
	} elseif ( 'filename' == $reftype ) {
		return $random_file;
	} elseif ( 'hyperlink' == $reftype ) {
		$url = get_settings('siteurl') . '/' . $dir . $random_file;
		return "<a href='$url' title='$random_filename'>$random_file</a>";
	} else {
		// Need to obtain location relative to root of domain (in case site is based out of subdirectory)
		preg_match("/^(https?:\/\/)?([^\/]+)\/?(.+)?$/", get_settings('siteurl'), $matches);
		$relpath = isset($matches[3]) ? '/' . $matches[3] : '';
		return $relpath . '/' . $dir . $random_file;
	}
} //end c2c_random_file()

?>
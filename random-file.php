<?php
/**
 * @package Random_File
 * @author Scott Reilly
 * @version 1.6
 */
/*
Plugin Name: Random File
Version: 1.6
Plugin URI: http://coffee2code.com/wp-plugins/random-file/
Author: Scott Reilly
Author URI: http://coffee2code.com
Description: Retrieve the name, path, or link to a randomly chosen file or files in a specified directory.

Compatible with WordPress 1.5+, 2.0+, 2.1+, 2.2+, 2.3+, 2.5+, 2.6+, 2.7+, 2.8+, 2.9+, 3.0+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/random-file/

*/

/*
Copyright (c) 2004-2010 by Scott Reilly (aka coffee2code)

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

if ( !function_exists( 'c2c_random_file' ) ):
/**
 * Retrieves the name, path, or link to a randomly chosen file in a specified
 *  directory.
 *
 * Details on the acceptable values for $reftype:
 *
 *   'relative' => A location relative to the primary domain:
 *      /journal/random/randomfile.txt
 *      [This is the default setting as it is the most applicable.  Relative
 *      referencing is necessary if the random file is to be used as an
 *      argument to include() or virtual().  It's also a valid way to reference
 *      a file for A HREF= and IMG SRC= linking.]
 *
 *   'absolute' => An absolute location relative to the root of the server's
 *      file system:
 *      /usr/local/htdocs/yoursite/www/journal/random/randomfile.txt
 *
 *   'url' => The URL of the random file:
 *      http://www.yoursite.org/journal/random/randomfile.txt
 *      [If you desire the use of full URL, i.e. for A HREF= or IMG SRC= link.]
 *
 *   'filename' => The filename of the random file:
 *      randomefile.txt
 *
 *   'hyperlink' => The filename of the random file hyperlinked to that random file:
 *      <a href='http://yoursite.org/journal/random/randomfile.txt' title='randomfile.txt'>randomfile.txt</a>
 *
 * @param string $dir The directory (relative to the root of the site) containing the files to be random chosen from
 * @param string $extensions (optional) A space-separated list of extensions, one of which the chosen file must have (case insensitive). Default is ''.
 * @param string $reftype (optional) One of: absolute, filename, hyperlink, relative, or url.  Default is 'relative'.
 * @param array $exclusions (optional) Filenames to exclude from consideration as a random file
 * @return string The random file chosen (if possible)
 */
function c2c_random_file( $dir, $extensions = '', $reftype = 'relative', $exclusions = array() ) {
	$files = array();
	$i = -1;
	$pattern = '/.*';

	if ( !empty( $extensions ) )
		$pattern .= '\.(' . implode( '|', explode( ' ', $extensions ) ) . ')';
	$pattern .= '$/i';

	$dir = trim( $dir, '/' );
	$abs_dir = ABSPATH . $dir;
	if ( !file_exists( $abs_dir ) )
		return;

	$handle = @opendir( $abs_dir );
	if ( false === $handle )
		return;

	$exclusions = empty( $exclusions ) ? array() : array_map( 'basename', $exclusions );

	while ( false != ( $file = readdir( $handle ) ) ) {
		if ( is_file( $abs_dir . '/' . $file ) && preg_match( $pattern, $file ) && !in_array( $file, $exclusions ) ) {
			$files[] = $file;
			++$i;
		}
	}

	closedir( $handle );

	if ( empty( $files ) )
		return;
	
	mt_srand( (double) microtime() * 1000000 );
	$rand = mt_rand( 0, $i );

	if ( !empty( $dir ) )
		$dir .= '/';

	$random_file = $files[$rand];

	if ( 'url' == $reftype )
		return get_option( 'siteurl' ) . '/' . $dir . $random_file;

	if ( 'absolute' == $reftype )
		return $abs_dir . '/' . $random_file;	/* could also do realpath($random_file); */

	if ( 'filename' == $reftype )
		return $random_file;

	if ( 'hyperlink' == $reftype ) {
		$url = get_option('siteurl') . '/' . $dir . $random_file;
		return "<a href='$url' title='$random_filename'>$random_file</a>";
	}

	// Need to obtain location relative to root of domain (in case site is based out of subdirectory)
	preg_match( "/^(https?:\/\/)?([^\/]+)\/?(.+)?$/", get_option( 'siteurl' ), $matches );
	$relpath = isset( $matches[3] ) ? '/' . $matches[3] : '';
	return $relpath . '/' . $dir . $random_file;
} //end c2c_random_file()
add_filter( 'c2c_random_file', 'c2c_random_file', 10, 4 );
endif;


if ( !function_exists( 'c2c_random_files' ) ) :
/**
 * Retrieves the name, path, or link to a specified number of randomly chosen
 * files in a specified directory.
 *
 * Note: the number of files returned will be UP TO the specified number.
 * Obviously, if the directly has less than that number of files, then
 * only those files can be returned
 *
 * (see docs for c2c_random_file() for more details regarding values for
 * arguments)
 *
 * @param int $number The number of random files to select from the specified directory
 * @param string $dir The directory (relative to the root of the site) containing the files to be random chosen from
 * @param string $extensions (optional) A space-separated list of extensions, one of which the chosen file must have (case insensitive). Default is ''.
 * @param string $reftype (optional) One of: absolute, filename, hyperlink, relative, or url.  Default is 'relative'.
 * @param array $exclusions (optional) Filenames to exclude from consideration as a random file
 * @return array The random files chosen (if possible)
 */
function c2c_random_files( $number, $dir, $extensions = '', $reftype = 'relative', $exclusions = array() ) {
	$number = intval( $number );
	$exclusions = (array) $exclusions;
	$files = array();
	for ( $i = 0; $i < $number; $i++ ) {
		$f = c2c_random_file( $dir, $extensions, $reftype, $exclusions );
		if ( !$f )
			break;
		$files[] = $f;
		$exclusions[] = $f;
	}
	return $files;
}
add_filter( 'c2c_random_files', 'c2c_random_files', 10, 5 );
endif;
?>
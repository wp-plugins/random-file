<?php
/*
Plugin Name: Random File
Version: 0.9
Plugin URI: http://www.coffee2code.com/wp-plugins/
Author: Scott Reilly
Author URI: http://www.coffee2code.com
Description: Retrieve the name of a randomly chosen file in a given directory.  Useful for displaying random images/logos or including text from random files onto your site (writing excerpts, multi-line quotes, etc).

=>> Visit the plugin's homepage for more information and latest updates  <<=

Installation:

1. Download the file http://www.coffee2code.com/wp-plugins/random-file.zip and unzip it into your /wp-content/plugins/ directory.
-OR-
Copy and paste the the code ( http://www.coffee2code.com/wp-plugins/random-file.phps ) into a file called random-file.php, and put that file into your /wp-content/plugins/ directory.
2. Activate the plugin from your WordPress admin 'Plugins' page.
3. Make use of the function in your template (see examples below).


Notes:

- If you want to actually display the name of the random file, be sure to 'echo' the results:
<?php echo random_file('/random'); ?>

- The directory of random files must exist at the directory structure level of your WordPress
installation or below. (i.e., if your site is installed on your server at
/usr/local/htdocs/yoursite/www/journal/, then the directory of random files you specified will assume
that as its base... so $dir='randomfiles' would be assumed to actually be:
/usr/local/htdocs/yoursite/www/journal/randomfiles/)

- Leading and trailing '/' are unnecessary... '/randomfiles/' == '/randomfiles' == 'randomfiles/' == 'randomfiles'

- $extensions can be a space-separated list of extensions (case insensitive), i.e. 'jpg gif png jpeg'

- Unless you limit the file search to only include a particular $extension, all files in
the specified $dir will be under consideration for random selection

- The reference to the randomly selected file can be returned in one of three ways:
[Assume your WordPress installation is at http://www.yoursite.org/journal/ and you've
invoked random_file('random/', 'txt', $reftype)]

$reftype = 'absolute'
=> An absolute location relative to the primary domain:
/journal/random/randomfile.txt
[This is the default setting as it is the most applicable.  Absolute referencing is necessary if
the random file is to be used as an argument to include() or virtual().  It's also a valid way
to reference a file for A HREF= and IMG SRC= linking.]

$reftype = 'serverabsolute'
=> An absolute location relative to the root of the server's file system:
/usr/local/htdocs/yoursite/www/journal/random/randomfile.txt

$reftype = 'url'
=> The URL of the random file:
http://www.yoursite.org/journal/random/randomfile.txt
[If you desire the use of full URL, such as for a A HREF= or IMG SRC= link.]

- Can be run inside or outside of "the loop."

Examples:

// Include random logo or image on your site:
<img alt="logo" class="logo" src="<?php echo random_file('/wp-images/logos/'); ?>" />

// Insert text from a random file (i.e. for random multi-line quotes):
<blockquote class='todayquote'>
   <?php virtual(random_file('/quotes/', 'txt')); ?>
</blockquote>

// If you wanted to source a random .php file
<?php include(random_file('/randomphp', 'php')); ?>

*/

/*
Copyright (c) 2004, Scott Reilly
Released under the BSD License
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following 
conditions are met:
   
     * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
     * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following 
disclaimer in the documentation and/or other materials provided with the distribution.
     * Neither the name of coffee2code.com nor the names of its contributors may be used to endorse or promote products derived from 
this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT 
NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL 
THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

function random_file ($dir, $extensions='', $reftype='relative') {
	$files = array();
	$i = -1;
	$pattern = '/.*';
	if (!empty($extensions)) $pattern .= '\.(' . implode('|', explode(' ', $extensions)) . ')';
	$pattern .= '$/i';
	$dir = trim($dir, '/');
	$handle = opendir(ABSPATH . $dir);
	while (false != ($file = readdir($handle))) {
		if (is_file(ABSPATH . $dir . '/' . $file) && preg_match($pattern, $file)) {
			$files[] = $file;
			++$i;
		}
	}
	closedir($handle);
	if (empty($files)) return;
	
	mt_srand((double)microtime()*1000000);
	$rand = mt_rand(0, $i);
	if (!empty($dir)) $dir .= '/';
	if ('url' == $reftype) {
		return get_settings('siteurl') . '/' . $dir . $files[$rand];
	} elseif ('absolute' == $reftype) {
		return ABSPATH . $dir . $files[$rand];
	} else {
		// Need to obtain location relative to root of domain (in case site is based out of subdirectory)
		preg_match("/^(https?:\/\/)?([^\/]+)\/?(.+)?$/", get_settings('siteurl'), $matches);
		$relpath = isset($matches[3]) ? '/' . $matches[3] : '';
		return $relpath . '/' . $dir . $files[$rand];
	}
} //end random_file()

?>
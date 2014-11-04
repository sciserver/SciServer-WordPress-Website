<?php
/**
 *     Random image gallery with pretty photo zoom
 *     Copyright (C) 2011 - 2014  www.gopiplus.com
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
$gCF_abspath = dirname(__FILE__);
$gCF_abspath_1 = str_replace('wp-content/plugins/random-image-gallery-with-pretty-photo-zoom', '', $gCF_abspath);
$gCF_abspath_1 = str_replace('wp-content\plugins\random-image-gallery-with-pretty-photo-zoom', '', $gCF_abspath_1);

$rigwppz_dir = $_GET["DIR"];

$max_height = 1000;
$image = $gCF_abspath_1 . $rigwppz_dir . $_GET["IMGNAME"];
$max_width = $_GET["MAXWIDTH"];

if($max_width > 500)
{
	$max_width = 500;
}
if (strrchr($image, '/')) {
	$filename = substr(strrchr($image, '/'), 1); // remove folder references
} else {
	$filename = $image;
}

$size = getimagesize($image);
$width = $size[0];
$height = $size[1];

// get the ratio needed
$x_ratio = $max_width / $width;
$y_ratio = $max_height / $height;

// if image already meets criteria, load current values in
// if not, use ratios to load new size info
if (($width <= $max_width) && ($height <= $max_height) ) {
	$tn_width = $width;
	$tn_height = $height;
} else if (($x_ratio * $height) < $max_height) {
	$tn_height = ceil($x_ratio * $height);
	$tn_width = $max_width;
} else {
	$tn_width = ceil($y_ratio * $width);
	$tn_height = $max_height;
}

/* Caching additions by Trent Davies */
// first check cache
// cache must be world-readable
$resized = 'cache/'.$tn_width.'x'.$tn_height.'-'.$filename;
$imageModified = @filemtime($image);
$thumbModified = @filemtime($resized);

header("Content-type: image/jpeg");

// if thumbnail is newer than image then output cached thumbnail and exit
if($imageModified<$thumbModified) {
	header("Last-Modified: ".gmdate("D, d M Y H:i:s",$thumbModified)." GMT");
	readfile($resized);
	exit;
}

// read image
$ext = substr(strrchr($image, '.'), 1); // get the file extension
switch ($ext) { 
	case 'jpg':     // jpg
		$src = imagecreatefromjpeg($image) or notfound();
		break;
	case 'png':     // png
		$src = imagecreatefrompng($image) or notfound();
		break;
	case 'gif':     // gif
		$src = imagecreatefromgif($image) or notfound();
		break;
	case 'JPG':     // jpg
		$src = imagecreatefromjpeg($image) or notfound();
		break;
	case 'PNG':     // png
		$src = imagecreatefrompng($image) or notfound();
		break;
	case 'GIF':     // gif
		$src = imagecreatefromgif($image) or notfound();
		break;

	default:
		notfound();
}

// set up canvas
$dst = imagecreatetruecolor($tn_width,$tn_height);

imageantialias ($dst, true);

// copy resized image to new canvas
imagecopyresampled ($dst, $src, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);

/* Sharpening adddition by Mike Harding */
// sharpen the image (only available in PHP5.1)
if (function_exists("imageconvolution")) {
	$matrix = array(    array( -1, -1, -1 ),
                    array( -1, 32, -1 ),
                    array( -1, -1, -1 ) );
	$divisor = 24;
	$offset = 0;

	imageconvolution($dst, $matrix, $divisor, $offset);
}

// send the header and new image
imagejpeg($dst, null, -1);
imagejpeg($dst, $resized, -1); // write the thumbnail to cache as well...

// clear out the resources
imagedestroy($src);
imagedestroy($dst);

?>

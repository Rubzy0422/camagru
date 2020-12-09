<?php 

function createImage($data, $name) {
	if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
		$data = substr($data, strpos($data, ',') + 1);
		$type = strtolower($type[1]); // jpg, png, gif
	
		if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
			return 'invalid image type';
		}
		$data = str_replace( ' ', '+', $data );
		$data = base64_decode($data);
	
		if ($data === false) {
			return 'base64_decode failed';
		}
	} else {
		return 'did not match data URI with image data';
	}
	// Delete File if exists
	if (file_exists("{$name}.{$type}"))
		unlink("{$name}.{$type}");
	// Create New Image 
	file_put_contents("{$name}.{$type}", $data);
}

function mergeImages($img1, $img2, $dst)
{
	list($width, $height, $type, $attr) = getimagesize($img1);
	$rawimg = imagecreatefrompng($img1);
	$stickerimg = imagecreatefrompng($img2);

	imagealphablending($rawimg, true);
	imagesavealpha($rawimg, true);

	imagecopy($rawimg, $stickerimg, 0, 0, 0, 0, $width, $height);
	imagepng($rawimg, $dst);

	imagedestroy($rawimg);
	imagedestroy($stickerimg);
}
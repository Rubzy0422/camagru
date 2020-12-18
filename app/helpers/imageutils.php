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


function UpdateImageFolder($postarr)
{
		// Get all files under image directory with extension

	// have a array that specifies what posts there is still ,
	// if the post is not in that list have it removed from system -- do this on user delete and post delete
	$keepimg = array();
	$ext = '.png';
	foreach($postarr as $post)
	{
		$img_dst = APPROOT. '/Images/combo/' . $post->id . $ext;
		$img_src = APPROOT. '/Images/userimages/' . $post->id . $ext;
		$img_stick = APPROOT. '/Images/stickers/' . $post->id . $ext;
		
		array_push($keepimg, $img_dst, $img_src, $img_stick);
	}
	$allImages = glob(APPROOT. '/Images/*/*' . $ext, GLOB_BRACE);

	foreach ($allImages as $image)
	{
		if (!in_array($image,$keepimg))
		{
			if (file_exists($image))
				unlink($image);
		}
	}
}
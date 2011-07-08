<?php
namespace modules\base\files\system\classes; 
/**
 *
 * Special class for processing image
 * @author Juri Em
 *
 */
final class Image {

	private $_fileName;
	private $_isImage;

	private $_width;
	private $_height;
	private $_mime;

	private $_image;

	/**
	 *
	 * Constructor
	 * @param String $fileName - path to image
	 */
	public function __construct($fileName, $type=null) {

		$this->_fileName = $fileName;
		$this->_isImage = false;

		//Get image info
		$image = getimagesize($fileName);

		if ($image) {
			$this->_width = $image[0];
			$this->_height = $image[1];
			$this->_mime = $image['mime'];
				
			$this->_isImage = true;
			$this->_createImage();
			if ($type !== null) {
				$this->_mime = 'image/'.$type;
			}
		}
	}

	/**
	 *
	 * Descructor
	 */
	public function __destruct() {
		if ($this->_isImage) {
			imagedestroy($this->_image);
		}
	}

	/**
	 *
	 * Resize image
	 * @param Int $width - new width
	 * @param Int $height - new height
	 */
	public function resize($width, $height) {
		if ($width != null && $height !== null) {
			if ($this->_isImage) {

				$_width = $this->_width;
				$_height = $this->_height;



				$_widthWeight = $width/$_width;
				$_heightWeight = $height/$_height;
				if ($_widthWeight > $_heightWeight) {
					$_weight = $_widthWeight;
				} else {
					$_weight = $_heightWeight;
				}
				$newWidth = $_width * $_weight;
				$newHeight = $_height * $_weight;
				if ($newHeight > $height || $newWidth > $width) {
					$_widthWeight = $width/$newWidth;
					$_heightWeight = $height/$newHeight;

					if ($_widthWeight < $_heightWeight) {
						$_weight = $_widthWeight;
					} else {
						$_weight = $_heightWeight;
					}
					$newWidth = $newWidth * $_weight;
					$newHeight = $newHeight * $_weight;
				}
				$resizeIt = true;

				//Resize image if need it
				if (isset($resizeIt)) {
					if ($this->_mime == 'image/gif') {
						$newImage = imagecreate($newWidth, $newHeight);
					} else {
						$newImage = imagecreatetruecolor($newWidth, $newHeight);
						imageAlphaBlending($newImage, false);
						imageSaveAlpha($newImage, true);
						if ($this->_mime == 'image/png') {
							$bgColor = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
							imagefill($newImage, 0, 0, $bgColor);
							//imagecolortransparent($newImage, $bgColor);
						}

							
					}
					imagecopyresampled($newImage, $this->_image, 0, 0, 0, 0, $newWidth, $newHeight, $_width, $_height);
					$this->_image = $newImage;


					//Create frame

					if ($this->_mime == 'image/gif') {
						$frame = imagecreate($width, $height);
					} else {
						$frame = imagecreatetruecolor($width, $height);
						imagesavealpha($frame, true);
					}

					if ($this->_mime == 'image/png') {
						$bgColor = imagecolorallocatealpha($frame, 255, 255, 255, 127);
						imagefill($frame, 0, 0, $bgColor);
					} else {
						$bgColor = imagecolorallocate($frame, 255, 255, 255);
						imagefill($frame, 0, 0, $bgColor);
					}

					//Calculate position
					$x = round(($width - $newWidth)/2);
					$y = round(($height - $newHeight)/2);
					//$x =0; $y =0;
					imagecopy($frame, $this->_image, $x, $y, 0, 0, $newWidth, $newHeight);
					//imagecopyresampled($frame, $this->_image, $x, $y, 0, 0, $newWidth, $newHeight, $newWidth, $newHeight);
					$this->_image = $frame;
				}
			}
		}
	}

	/**
	 *
	 * Return original file
	 */
	public function original() {
		if ($this->_isImage) {
			header('Content-type:*');
			readfile($this->_fileName);
		}
	}

	/**
	 *
	 * Show image
	 */
	public function show() {

		if ($this->_isImage) {
			header('Content-type: '.$this->_mime);
			switch ($this->_mime) {
				case 'image/gif':
					imagegif($this->_image);
					break;
				case 'image/jpeg':
					imagejpeg($this->_image);
					break;
				default:
					imagepng($this->_image);

			}
		}
	}

	/**
	 *
	 * Help function for creting image
	 */
	private function _createImage() {


		if ($this->_mime == 'image/jpeg') {
			$this->_image = imagecreatefromjpeg($this->_fileName);
		} elseif ($this->_mime == 'image/png') {
			$this->_image = imagecreatefrompng($this->_fileName);
			imagesavealpha($this->_image, true);
		} elseif ($this->_mime == 'image/gif') {
			$this->_image = imagecreatefromgif($this->_fileName);
		} else {	
			// Open bmp 
			$this->_image = $this->imagecreatefrombmp($this->_fileName);
		}
	}

	// Open BMP image
	private function imagecreatefrombmp($p_sFile)
	{
		$file    =    fopen($p_sFile,"rb");
		$read    =    fread($file,10);
		while(!feof($file)&&($read<>""))
		$read    .=    fread($file,1024);
		$temp    =    unpack("H*",$read);
		$hex    =    $temp[1];
		$header    =    substr($hex,0,108);
		if (substr($header,0,4)=="424d")
		{
			$header_parts    =    str_split($header,2);
			$width            =    hexdec($header_parts[19].$header_parts[18]);
			$height            =    hexdec($header_parts[23].$header_parts[22]);
			unset($header_parts);
		}
		$x                =    0;
		$y                =    1;
		$image            =    imagecreatetruecolor($width,$height);
		$body            =    substr($hex,108);
		$body_size        =    (strlen($body)/2);
		$header_size    =    ($width*$height);
		$usePadding        =    ($body_size>($header_size*3)+4);
		for ($i=0;$i<$body_size;$i+=3)
		{
			if ($x>=$width)
			{
				if ($usePadding)
				$i    +=    $width%4;
				$x    =    0;
				$y++;
				if ($y>$height)
				break;
			}
			$i_pos    =    $i*2;
			$r        =    hexdec($body[$i_pos+4].$body[$i_pos+5]);
			$g        =    hexdec($body[$i_pos+2].$body[$i_pos+3]);
			$b        =    hexdec($body[$i_pos].$body[$i_pos+1]);
			$color    =    imagecolorallocate($image,$r,$g,$b);
			imagesetpixel($image,$x,$height-$y,$color);
			$x++;
		}
		unset($body);
		return $image;
	}


}
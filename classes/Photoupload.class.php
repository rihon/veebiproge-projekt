<?php
	class Photoupload{
		/*public $publicTest;
		private $privateTest;*/
		private $tempName;
		private $fileType;
		private $textToImage;
		private $myTempImage;
		private $myImage;
		private $marginRight;
		private $marginBottom;
		public $target_file;
		public  $exifToImage;
		
		
		function __construct($fileToUpload, $fileType){
			$this->tempName = $fileToUpload;
			$this->imageFileType = $fileType;
			$this->marginRight = 10;
			$this->marginBottom= 10;
			/*$this->publicTest = "Täitsa avalik";
			$this->privateTest= $x;*/
		}//function__construct lõppeb
		public function readExif(){
			//loeme EXIF infot, millal pilt tehti
			@$exif = exif_read_data($this->tempName, "ANY_TAG", 0, true);
			//var_dump($exif);
			if(!empty($exif["DateTimeOriginal"])){
				$this->textToImage = "Pilt tehti: " .$exif["DateTimeOriginal"];
			} else {
				$this->textToImage = "Pildistamise aeg teadmata!";
			}
		}//function readExif lõppeb
		private function createImage(){
			if($this->imageFileType == "jpg" or $this->imageFileType == "jpeg"){
				$this->myTempImage = imagecreatefromjpeg($this->tempName);
			}
			if($this->imageFileType == "png"){
				$this->myTempImage = imagecreatefrompng($this->tempName);
			}
			if($this->imageFileType == "gif"){
				$this->myTempImage = imagecreatefromgif($this->tempName);
			}			
		}//Function createImage lõppeb
		
		public function resizeImage($width, $height){
			//küsime originaalsuurust
			$this->createImage();
			$imageWidth = imagesx($this->myTempImage);
			$imageHeight = imagesy($this->myTempImage);
			$sizeRatio = 1;
			if($imageWidth > $imageHeight){
				$sizeRatio = $imageWidth / $width;
			} else {
				$sizeRatio = $imageHeight / $height;
			}
			$this->myImage = $this->resize_image($this->myTempImage, $imageWidth, $imageHeight, round($imageWidth / $sizeRatio), round($imageHeight / $sizeRatio));
		}//resizeImage lõppeb
		
		private function resize_image($image, $origW, $origH, $w, $h){
			$dst = imagecreatetruecolor($w, $h);
			//asendame musta värvi pikslid läbipaistvatega, et säilitada vajadusel png piltide läbipaistvus
			imagesavealpha($dst, true);
			$transcolor = imagecolorallocatealpha($dst, 0, 0, 0, 127);
			imagefill($dst, 0, 0, $transcolor);
			imagecopyresampled($dst, $image,0, 0, 0, 0, $w, $h, $origW, $origH);
			return $dst; 
		}//function resize image lõppeb
			/*vesimärgi lisamine
		public function addWatermark(){
			$stamp = imagecreatefrompng("../graphics/hmv_logo.png");
			$stampWidth = imagesx($stamp);
			$stampHeight = imagesy($stamp);
			$stampPosX = imagesx($this->myImage) - $stampWidth - $this->marginRight;
			$stampPosY = imagesy($this->myImage) - $stampHeight - $this->marginBottom;
			imageCopy($this->myImage, $stamp, $stampPosX, $stampPosY, 0, 0, $stampWidth, $stampHeight);
		}//function addWatermark lõppeb
		
		public function addTextWatermark($text){
		//lisame ka teksti vesimärgina
			$textColor = imagecolorallocatealpha($this->myImage, 150, 150, 150, 50);
			//RGBA alpha 0 -127
			imagettftext($this->myImage, 20, 0, 10, 25, $textColor, "../graphics/ARIAL.TTF", $text);
		}//function addTextWatermark lõppeb*/
		
		
		public function savePhoto($directory, $fileName){
			$target_file= $directory .$fileName;
			$notice="";
			if($this->imageFileType == "jpg" or $this->imageFileType == "jpeg"){
				if(imagejpeg($this->myImage, $target_file, 90)){
					$notice .="true";
				} else {
					$notice .= "false";
				}
			}
			if($this->imageFileType == "png"){
				if(imagepng($this->myImage, $target_file, 6)){
					$notice = "true";
				} else {
					$notice .= "false";
				}
			}
			if($this->imageFileType == "gif"){
				if(imagegif($this->myImage, $target_file)){
					$notice = "true";
				} else {
					$notice .= "false";
				}
			}
		}//function savephoto lõppeb
		
		public function clearImages(){
			imagedestroy($this->myImage);
			imagedestroy($this->myTempImage);
		}//function clearImages lõppeb		
		
		
		public function photoToDatabase($target_file){
		
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("INSERT INTO TLUnder_photo (id, userid, filename) VALUES (?, ?, ?);");
	
		echo $mysqli->error;
		$stmt->bind_param("iis", $_SESSION["userId"], $_SESSION["userId"], $target_file);
		if($stmt->execute()){
			$notice = "Pilt on salvestatud!";
		} else {
			$notice = "Pildi salvestamisel tekkis viga: " .$stmt->error;
		}
		$stmt->close();
		$mysqli->close();
		return $notice;
		}	
	}//class lõppeb
	

	
	

?>
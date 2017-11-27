<?php
require("functions.php");
require("aboutfunction.php");
	$notice = "";
	$allIdeas = "";
	
	
//Liidan klassi
	require("classes/Photoupload.class.php");
	
	function convertImage($originalImage, $outputImage, $quality)
	{// jpg, png, gif or bmp?
    $exploded = explode('.',$originalImage);
    $ext = $exploded[count($exploded) - 1]; 

    if (preg_match('/jpg|jpeg/i',$ext))
        $imageTmp=imagecreatefromjpeg($originalImage);
    else if (preg_match('/png/i',$ext))
        $imageTmp=imagecreatefrompng($originalImage);
    else if (preg_match('/gif/i',$ext))
        $imageTmp=imagecreatefromgif($originalImage);
    else if (preg_match('/bmp/i',$ext))
        $imageTmp=imagecreatefrombmp($originalImage);
    else
        return 0;

    // quality is a value from 0 (worst) to 100 (best)
    imagejpeg($imageTmp, $outputImage, $quality);
    imagedestroy($imageTmp);

    return 1;
	}
	
	//kui pole sisseloginud, siis sisselogimise lehele
	if(!isset($_SESSION["userId"])){
		header("Location: login.php");
		exit();
	}
	
	//kui logib välja
	if (isset($_GET["logout"])){
		//lõpetame sessiooni
		session_destroy();
		header("Location: login.php");
	}
	
	//Algab foto laadimise osa
	$target_dir = "uploads/";
	$target_file;
	$uploadOk = 1;
	$imageFileType;
	$maxWidth = 600;
	$maxHeight = 400;
	$marginBottom = 10;
	$marginRight = 10;
	
	//Kas on pildi failitüüp
	if(isset($_POST["submit"])) {
		
		//kas mingi fail valiti
		if(!empty($_FILES["fileToUpload"]["name"])){
			
			$imageFileType = strtolower(pathinfo(basename($_FILES["fileToUpload"]["name"]))["extension"]);
			//$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			//tekitame failinime koos ajatempliga
			//$target_file = $target_dir ."hmv_" .(microtime(1) * 10000) ."." .$imageFileType;
			//$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			
			$target_file =($_SESSION["userId"]) ."." .$imageFileType;
			
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				$notice .= "Fail on pilt - " . $check["mime"] . ". ";
				$uploadOk = 1;
			} else {
				$notice .= "See pole pildifail. ";
				$uploadOk = 0;
			}
			
			//Kas selline pilt on juba üles laetud
			if (file_exists($target_file)) {
				$notice .= "Kahjuks on selle nimega pilt juba olemas. ";
				$uploadOk = 0;
			}
			
			//Piirame faili suuruse
			if ($_FILES["fileToUpload"]["size"] > 1000000) {
				$notice .= "Pilt on liiga suur! ";
				$uploadOk = 0;
			}
			
			//Piirame failitüüpe
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
				$notice .= "Vabandust, vaid jpg, jpeg, png ja gif failid on lubatud! ";
				$uploadOk = 0;
			}
			

			if ($uploadOk == 0) {
				$notice .= "Vabandust, pilti ei laetud üles! ";
			//Kui saab üles laadida
			} else {

				//kasutan klassi
				$myPhoto = new Photoupload($_FILES["fileToUpload"]["tmp_name"], $imageFileType);
				$myPhoto->readExif();
				$myPhoto->resizeImage($maxWidth, $maxHeight);
				//$myPhoto->addWatermark();
				//$myPhoto->addTextWatermark($myPhoto->exifToImage);
				//$myPhoto->addTextWatermark("hmv_foto");
				$myPhoto->savePhoto($target_dir, $target_file);
				$myPhoto->clearImages();
				$myPhoto->photoToDatabase($target_file);
				unset ($myPhoto);
				
			}
		
		} else {
			$notice = "Palun valige kõigepealt pildifail!";
		} //kas üldse mõni fail valiti, lõppeb
	}//kas vajutati submit nuppu, lõppeb

	
	//kui soovitakse iseloomustust salvestada
	if(isset($_POST["aboutBtn"])){
		
		if(isset($_POST["about"])and !empty($_POST["about"])){
			$myAbout = test_input($_POST["about"]);
			$notice = saveIseloom($myAbout);
		}
	}	
	
//UUENDAMISE MOODULID
//Kui iseloomu pole siis esimene variant
//Kui iseloom on siis uuendamine
if(isset($_POST["aboutBtn"])){
	$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $mysqli->prepare("SELECT id FROM TLUnder_user_profile WHERE id = ?");
	$stmt->bind_param("i", $_SESSION["userId"]);
	$stmt->bind_result($result);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
	$mysqli->close();
	if(($result)==($_SESSION["userId"])){
	//Iseloom on olemas, tuleb uuendada
			if(isset($_POST["about"])and !empty($_POST["about"])){
			$myAbout = test_input($_POST["about"]);
			$notice = updateIseloom($myAbout);
		}
	}else{
		//Iseloomu pole, tuleb salvestada
		if(isset($_POST["about"])and !empty($_POST["about"])){
			$myAbout = test_input($_POST["about"]);
			$notice = saveIseloom($myAbout);
		}
	}

		//kas uuendatakse
		if (isset($_POST["update"])){
			echo "hakkab uuendama!";
			echo $_POST["id"];
			updateIdea($_POST["id"], test_input($_POST["about"]));
			header("Location: profiiliredigeerimine.php");
			exit();
		}
}
?>





<?php require ("header.php")?>
<h2>Siin on võimalik enda kasutajakontot redigeerida</h2>

<?php showEditPicture();?>


<h2>Foto üleslaadimine</h2>
	<form action="profiiliredigeerimine.php" method="post" enctype="multipart/form-data">
		<label>Valige pildifail:</label>
		<input type="file" name="fileToUpload" id="fileToUpload">
		<input type="submit" value="Lae üles" name="submit" id="submitPhoto"><span id="fileSizeError"></span>
	</form>
	
<h2>Enda imelise iseloomu tutvustamine</h2>
<p><?php getSingleAboutData();?></p>
<h2>Lisa enda iseloomustus</h2>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<label>Muuda iseloomustust </label>
		<input name="about" type="text">
		<input name="aboutBtn" type="submit" value="Muuda">
	</form>

	<p><?php echo $notice;?></p>


<?php require("footer.php") ?>
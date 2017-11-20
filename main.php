 <?php
	require("functions.php");
	
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
		exit();
	}
	//$dirToRead = "../uploads/";
	//kuna tahan ainult pildifaile, siis filtreerin
	$picFileTypes = ["jpg", "jpeg", "png", "gif"];
	$picFiles = [];
	$allFiles = array_slice(scandir($dirToRead),2);
	//var_dump($allFiles);
	
	//tsükkel, mis töötab ainult massiividega
	foreach ($allFiles as $file){
		$fileType = pathinfo($file, PATHINFO_EXTENSION);
		//kas see tüüp on lubatud nimekirjas
		if (in_array($fileType, $picFileTypes) == true){
			array_push($picFiles, $file);
			//$picFiles[] = $file;
		}
	}//foreach lõppeb
	//var_dump($picFiles);
	
	//mitu pilti on?
	$fileCount = count($picFiles);
	$picNumber = mt_rand(0, $fileCount - 1);
	$picToShow = $picFiles[$picNumber];
//TEST123
	?>

	<?php require ("header.php")?>
	
	<p>Selle lehe nimi on TLUnder.</p>
	<p><a href="?logout=1">Logi välja</a>!</p>
	<p><a href="usersinfo.php">Kasutajate info</a></p><!--Viib kasutajate lehele et leida kaaslast-->
	<p><a href=>Kasutajakonto redigeerimine</a></p><!--Kasutajakonto redigeerimine, muuta saab tutvustust ja pilte-->
	
	<!--<p>Üks pilt Tallinna Ülikoolist!</p>
	<img src="<?php //echo $dirToRead .$picToShow; ?>" alt="Tallinna Ülikool">-->
<?php require("footer.php") ?>
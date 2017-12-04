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
	?>

	<?php require ("header.php")?>
	
	<p><a href="usersinfo.php">Kasutajate info</a></p><!--Viib kasutajate lehele et leida kaaslast-->
	<p><a href="profiiliredigeerimine.php">Kasutajakonto redigeerimine</a></p><!--Kasutajakonto redigeerimine, muuta saab tutvustust ja pilte-->

<?php require("footer.php") ?>
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
<p>Siin on kõigi registreeritud kasutajate nimed</p>

<?php allUsers();?>

<?php require("footer.php") ?>
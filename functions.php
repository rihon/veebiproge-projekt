<?php
	require("config.php");
	$database = "if17_riho_4";
	
	//alustan sessiooni
	session_start();
	
	//sisselogimise funktsioon
	function signIn($email, $password){
		$notice = "";
		//ühendus serveriga
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT id, firstname, lastname, email, password FROM TLUnder_users WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->bind_result($id, $firstnameFromDb, $lastnameFromDb, $emailFromDb, $passwordFromDb);
		$stmt->execute();
		
		//kontrollime vastavust
		if ($stmt->fetch()){
			$hash = hash("sha512", $password);
			if ($hash == $passwordFromDb){
				$notice = "Logisite sisse!";
				
				//Määran sessiooni muutujad
				$_SESSION["userId"] = $id;
				$_SESSION["firstname"] = $firstnameFromDb;
				$_SESSION["lastname"] = $lastnameFromDb;
				$_SESSION["userEmail"] = $emailFromDb;
				
				//liigume edasi pealehele (main.php)
				header("Location: main.php");
				exit();
			} else {
				$notice = "Vale salasõna!";
			}
		} else {
			$notice = 'Sellise kasutajatunnusega "' .$email .'" pole registreeritud!';
		}
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	
	//kasutaja salvestamise funktsioon
	function signUp($signupFirstName, $signupFamilyName, $signupBirthDate, $gender, $signupEmail, $signupPassword){
		//loome andmebaasiühenduse
		
	$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		//valmistame ette käsu andmebaasiserverile
		$stmt = $mysqli->prepare("INSERT INTO TLUnder_users (firstname, lastname, birthday, gender, email, password) VALUES (?, ?, ?, ?, ?, ?)");
		echo $mysqli->error;
		//s - string
		//i - integer
		//d - decimal
		$stmt->bind_param("sssiss", $signupFirstName, $signupFamilyName, $signupBirthDate, $gender, $signupEmail, $signupPassword);
		//$stmt->execute();
		if ($stmt->execute()){
			echo "\n Õnnestus!";
		} else {
			echo "\n Tekkis viga : " .$stmt->error;
		}
		$stmt->close();
		$mysqli->close();
	}
	
	//mõtete salvestamine
	/*function saveIdea($idea, $color){
		//echo $color;
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("INSERT INTO vpuserideas (userid, idea, ideacolor) VALUES (?, ?, ?)");
		echo $mysqli->error;
		$stmt->bind_param("iss", $_SESSION["userId"], $idea, $color);
		if($stmt->execute()){
			$notice = "Mõte on salvestatud!";
		} else {
			$notice = "Mõtte salvestamisel tekkis viga: " .$stmt->error;
		}
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	*/
	//kõikide ideede lugemise funktsioon
	/*function readAllIdeas(){
		$ideasHTML = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		//$stmt = $mysqli->prepare("SELECT idea, ideaColor FROM vpuserideas WHERE userid = ?");
		$stmt = $mysqli->prepare("SELECT id, idea, ideaColor FROM vpuserideas WHERE userid = ? ORDER BY id DESC");
		$stmt->bind_param("i", $_SESSION["userId"]);
		$stmt->bind_result($ideaId, $idea, $color);
		$stmt->execute();
		//$result = array();//?
		while ($stmt->fetch()){
			$ideasHTML .= '<p style="background-color: ' .$color .'">' .$idea .' | <a href="ideaedit.php?id=' .$ideaId .'">Toimeta</a>' ."</p> \n";
			//link: <a href="ideaedit.php?id=4"> Toimeta</a>
		}
		$stmt->close();
		$mysqli->close();
		return $ideasHTML;
	}
	*/
	//uusima idee lugemine
	/*function latestIdea(){
		//$ideaHTML = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT idea FROM vpuserideas WHERE id = (SELECT MAX(id) FROM vpuserideas)");
		//$stmt->bind_param("i", $last_id);
		//echo "Viga: " .$mysqli->error;
		$stmt->bind_result($idea);
		/*if($stmt->execute()){
			echo "Hea" .$idea;
			//$ideaHTML .= $idea;
		} else {
			echo "Tekkis viga: " .$stmt->error;
		}*/
		/*$stmt->execute();
		$stmt->fetch();//nüüd jääb meelde, kui fetch() ei tee, andmeid ei saa!
		$stmt->close();
		$mysqli->close();
		return $idea;
	}
	*/
	//sisestuse kontrollimise funktsioon
	
	
	//Kõikide kasutajate list
	function allUsers(){
	$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $mysqli->prepare("SELECT firstname, lastname, id FROM TLUnder_users");	
	$stmt->bind_result($FirstName, $FamilyName, $id);
	$stmt->execute();
	while ($stmt->fetch()){
		echo "<a href=profiil.php?userId=" .$id .">" .$FirstName ." " .$FamilyName ."</a></br>";
		
	}
	$notice= $mysqli->error;
	$stmt->close();
	$mysqli->close();
	
	}

	
	function showEditPicture(){
		$images = ("uploads/");
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT filename FROM TLUnder_photo WHERE userid=?");
		$stmt->bind_param("i", $_SESSION["userId"]);
		$stmt->bind_result($showEditPicture);
		$stmt->execute();
		$stmt->fetch();
		echo '<img src="'.$images .$showEditPicture .'" alt="'.$showEditPicture .'">';
		$stmt->close();
		$mysqli->close();
		
		
	}
	
	function test_input($data){
		$data = trim($data);//ebavajalikud tühiku jms eemaldada
		$data = stripslashes($data);//kaldkriipsud jms eemaldada
		$data = htmlspecialchars($data);//keelatud sümbolid
		return $data;
	}
	
	function saveIseloom($about){
		//echo $color;
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("INSERT INTO TLUnder_user_profile (id, userid, text) VALUES (?,?,?)");
		echo $mysqli->error;
		$stmt->bind_param("iis", $_SESSION["userId"], $_SESSION["userId"],$about);
		if($stmt->execute()){
			$notice = "Iseloom on muudetud!";
		} else {
			$notice = "Iseloomu muutmisel tekkis viga: " .$stmt->error;
		}
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	
		function updateIseloom($about){
		//echo $color;
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("UPDATE TLUnder_user_profile SET text = ? WHERE id=?");
		echo $mysqli->error;
		$stmt->bind_param("si" ,$about ,$_SESSION["userId"]);
		if($stmt->execute()){
			$notice = "Iseloom on muudetud!";
		} else {
			$notice = "Iseloomu muutmisel tekkis viga: " .$stmt->error;
		}
		$stmt->close();
		$mysqli->close();
		return $notice;
	} 
	
	
	
?>
<?php
	require("../config.php");
	$database = "if17_riho_4";
	//ühe konkreetse mõtte lugemine
	function getSingleAboutData(){
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT text FROM TLUnder_user_profile WHERE id=?");
		$stmt->bind_param("i", $_SESSION["userId"]);
		$stmt->bind_result($aboutText);
		$stmt->execute();
		$stmt->fetch();
		echo $aboutText;		
		$stmt->close();
		$mysqli->close();
		
		
	}
	
	function updateIdea($id, $idea, $color){
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("UPDATE TLUnder_user_profile SET text=? WHERE id=? AND deleted IS NULL");
		echo $mysqli->error;
		 //AND deleted IS NULL
		$stmt->bind_param("si", $about, $_SESSION["userId"]);
		if($stmt->execute()){
			echo "Õnnestus!";
		} else {
			echo $stmt->error;
		}
			
		$stmt->close();
		$mysqli->close();
	}
?>
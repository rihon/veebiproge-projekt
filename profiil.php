<?php
$userId =($_GET['userId']);
#echo $userId;
require("functions.php");
require("aboutfunction.php");

	$notice = "";
	$allIdeas = "";

function getSingleAboutDataProfile(){
	$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $mysqli->prepare("SELECT text FROM TLUnder_user_profile WHERE id=?");
	echo $mysqli->error;
	$stmt->bind_param("i", $GLOBALS["userId"]);
	$stmt->bind_result($aboutText);
	$stmt->execute();
	$stmt->fetch();
	echo $aboutText;		
	$stmt->close();
	$mysqli->close();
		
		
	}
	
function showPicture(){
		$images = ("uploads/");
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT filename FROM TLUnder_photo WHERE userid=?");
		echo $mysqli->error;
		$stmt->bind_param("i", $GLOBALS["userId"]);
		$stmt->bind_result($showEditPicture);
		$stmt->execute();
		$stmt->fetch();
		echo '<img src="'.$images .$showEditPicture .'" alt="'.$showEditPicture .'">';
		$stmt->close();
		$mysqli->close();
		
		
	}
$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
$stmt = $mysqli->prepare("SELECT firstname, lastname FROM TLUnder_users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->bind_result($firstname, $lastname);
$stmt->execute();
$stmt->fetch();
$stmt->close();
$mysqli->close();


?>


<?php require ("header.php")?>
<h1>Siin on kasutaja <?php echo $firstname ." " .$lastname ?> profiil</h1>

<?php showPicture();?>
<p><?php getSingleAboutDataProfile();?></p>
<?php require("footer.php") ?>
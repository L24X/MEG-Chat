<?php
require_once("../../../logic/db.php");

if(isset($_SESSION['pupil'])){
	$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
	$stmtCheck->execute(array('id' => $_SESSION['pupil']));
	$pupil_data = (array)$stmtCheck->fetchObject();
}

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MEG Chat | Werkzeuge | Quiz</title>
        <meta name="description" content="Erstellen Sie ganz einfach Quize mit dem MEG Quiz Ersteller. Kostenlos einfach online im Browser benutzbar. Erstellen Sie ein Quiz und spielen es mit der ganzen Klasse.">
        <meta name="keywords" lang="de" content="meg, max ernst gymnasium, quiz, erstellen, kostenlos, online, kahoot, fragen, spielen">
        <?php require('../../../middleware/head.php'); ?>
    </head>
    <body>
		<?php require('../../../middleware/navbar.php'); ?>
		<div id="site_container">
		    <div style="margin-top: 80px; ">
		        <h1>Quiz Erstellen</h1>
		    </div>
		</div>
	</body>
</html>
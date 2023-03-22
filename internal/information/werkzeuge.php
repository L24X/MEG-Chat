<?php
require_once("../logic/db.php");

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
        <title>MEG Chat | Werkzeuge</title>
        <meta name="description" content="Hier finden Sie alle möglichen von Schülern programmierte nützliche Tools. Schüler des Max Ernst Gymnasiums können diese gerne für Ihre Projekte nutzen.">
        <meta name="keywords" lang="de" content="meg, max ernst gymnasium, tools, werkzeuge, kostenlos, free, einfach, bearbeiten, online">
        <?php require('../middleware/head.php'); ?>
    </head>
    <body>
		<?php require('../middleware/navbar.php'); ?>
		<div id="site_container">
		    <div style="margin-top: 80px; text-align: center; ">
		        <h1>Tools & Werkzeuge</h1>
		        <p style="font-size: 18px; margin-top: 20px; ">Hier finden Sie alle möglichen von Schülern programmierte nützliche Tools. Schüler des Max Ernst Gymnasiums können diese gerne für Ihre Projekte nutzen.</p>
                <ul style="margin-top: 50px; ">
                    <li><a href="/werkzeuge/quiz/">Quiz erstellen</a></li>
                    <li><a href="/werkzeuge/ufc/">Audio Dateien umwandeln</a></li>
                </ul>
		    </div>
		</div>
	</body>
</html>
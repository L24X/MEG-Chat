<?php
require_once("internal/logic/db.php");

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
        <title>MEG Chat | Startseite</title>
        <meta name="description" content="Der Max Ernst Gymnasium Schüler Chat! Hier tauschen sich Schüler über Ihre Schule und alles was damit zusammenhängt aus. WhatsApp is out!">
        <meta name="keywords" lang="de" content="meg, max, ernst, gymnasium, chat, online, schueler, chatten, austauschen, hausaufgaben, fragen">
        <? require('internal/middleware/head.php'); ?>
    </head>
    <body>
		<? require('internal/middleware/navbar.php'); ?>
        <div id="site_container">
			<div style="width: 100%; height: 100%; margin-top: 180px; " class="centriert">
			    <div style="width: 600px; max-width: 95%; text-align: center; min-height: 400px; height: auto; ">
			        <h1 style="font-size: 28px; font-weight: bold; ">Der MEG-Chat! - Das sind wir und das wollen wir erreichen!</h1>
			        <div style="font-size: 18px; margin-top: 10px; ">
			          <p>Willkommen beim MEG-Chat, der Website für Schüler des Max Ernst Gymnasiums in Brühl! Wir bieten eine Plattform für Schüler, um sich auszutauschen und vernetzen zu können.</p>
			          <p>Auf unserem Portal kannst du Projekte vorstellen, nützliche Werkzeuge für den Unterricht finden, unsere online Schülerzeitung und unseren Blog lesen und dich über wichtige Neuigkeiten und Informationen auf dem Laufenden halten.</p>
			          <p>Unser Ziel ist es, die Lernplattform der Schule zu ergänzen und fehlende Funktionen anzubieten, um das Lernen und den Austausch unter Schülern zu erleichtern. Wir freuen uns auf deine Teilnahme!</p>
			        </div>
			        <a href="javascript:page_naviagte('/impressum');">Impressum</a>
			    </div>
			</div>
		</div>
    </body>
</html>

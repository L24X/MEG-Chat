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
        <div>
			<div style="width: 100%; margin-top: 20px; ">
		        <div id="all_container">
                    <?php require("internal/information/public_chats.php"); ?>
				</div>
                <?php require("internal/information/beliebteste_schueler.php"); ?>
                <?php require("internal/information/blog_news.php"); ?>
                <?php require("internal/information/projects.php"); ?>
			</div>
		</div>
    </body>
</html>

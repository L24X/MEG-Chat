<?php
require_once("../logic/db.php");

if(isset($_SESSION['pupil'])){
	$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
	$stmtCheck->execute(array('id' => $_SESSION['pupil']));
	$pupil_data = (array)$stmtCheck->fetchObject();
}

$s = $_GET['schueler'];

$stmtData = $db->prepare("SELECT ".DBTBL.".pupils.*, COUNT(".DBTBL.".pupils_votes.s_to) AS rating_count, COALESCE(SUM(points),0) as rating FROM ".DBTBL.".pupils LEFT JOIN ".DBTBL.".pupils_votes ON ".DBTBL.".pupils.id = ".DBTBL.".pupils_votes.s_to WHERE pupils.id = :id GROUP BY ".DBTBL.".pupils.id;");
$stmtData->execute(array('id' => $s));
$row = $stmtData->fetchObject();
$s_data = (array)$row;

if(isset($_SERVER['HTTP_USER_AGENT'])){
    $is_mobile = preg_match("/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|boost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
} else {
	$is_mobile = isset($_COOKIE['desktop']) ? ($_COOKIE['desktop'] == "a") : true;
}
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MEG Chat | Darum sollten Sie Ihr Kind aufs Max Ernst Gymnasium schicken!</title>
        <meta name="description" content="Darum sollten Sie Ihr Kind aufs Max Ernst Gymnasium schicken! Wir sind eine nette Schulgemeinschaft mit engagierten Leheren. Unsere Schule bietet den Schülern viele verschiedene Kurse um Ihre eigene persönliche Berufliche Laufbahn zu finden.">
        <meta name="keywords" lang="de" content="max ernst gymnasium, meg, darum meg, lehrer, kinder, gemeinschaft, schüler, angebote, information">
        <?php require('../middleware/head.php'); ?>
    </head>
    <body>
		<?php require('../middleware/navbar.php'); ?>
		<div id="site_container">
			<div style="position: fixed; top: 80px; left: 0px; right: 0px; bottom: 0px; overflow-y: auto; " class="centriert">
			    <div style="width: 700px; max-width: 100%; margin-top: 20px; text-align: center;">
			        <h1>Darum sollten Sie Ihr Kind aufs Max Ernst Gymnasium schicken!</h1>
			        <p style="float: left; text-align: left; font-size: 16px; margin-top: 25px; ">
			            Liebe Eltern, Liebe Kinder,
			        </p>
			        <p style="float: left; text-align: left; font-size: 16px; ">
			            ich möchte Sie heute überzeugen, Ihr Kind auf das Max Ernst Gymnasium in Brühl zu bringen. Das Gymnasium ist das erste und älteste zwischen Köln und Bonn und kann somit auf eine lange und erfolgreiche Geschichte zurückblicken. Die Schulgemeinschaft ist sehr nett und die Lehrer sind engagiert und kompetent.
			        </p>
			        <p style="float: left; text-align: left; font-size: 16px; ">
			            Die Schule fühlt sich in ihrem Schulprogramm dem Musischen und Humanitärem und einer weltoffenen Toleranz verpflichtet, was sich in Austauschprogrammen mit der französischen Partnerstadt Sceaux und Israel widerspiegelt. Durch diese Erfahrungen wird Ihr Kind in seiner Persönlichkeit gestärkt und lernt, andere Kulturen zu respektieren.
			        </p>
			        <p style="float: left; text-align: left; font-size: 16px; ">
			            Das Schulgebäude wurde in den Jahren 1962 bis 1965 von Peter Busmann geplant und gebaut und erhielt als erste Schule den Kölner Architekturpreis. Im Herbst 2002 wurde das Gebäude erweitert und bietet heute Platz für eine vierzügige Schule mit zwei Gebäudeteilen und einem Pavillon mit Unterrichtsräumen, Bibliothek, Medienraum und Musikraum. Besonders bemerkenswert ist der mit Glas überdachte und intensiv begrünte Innenhof als ganzjähriger Pausenhof.
			        </p>
			        <p style="float: left; text-align: left; font-size: 16px; ">
			            Das Max Ernst Gymnasium betreibt auf dem Schulgelände einen Geologischen Garten und Steinabgüsse von Kunst- und Geschichtswerken. Die Schule besitzt zudem bibliophile Raritäten, wie zum Beispiel eine Ausgabe der Cronica van der hilliger stat von Coellen von Johann Koelhoff aus dem Jahr 1499 und eine Artothek mit 130 Kunstblättern.
			        </p>
			        <p style="float: left; text-align: left; font-size: 16px; ">
			            Eine weitere Besonderheit der Schule ist, dass sie seit dem Schuljahr 2013/14 für Mittelstufenschüler eine berufsorientierende Förderung durch die von der Industrie- und Handelskammer zu Köln anerkannte Maßnahme "Profil AC" anbietet. Dadurch kann Ihr Kind frühzeitig seine Talente und Interessen entdecken und sich gezielt auf seine Zukunft vorbereiten.
			        </p>
			        <p style="float: left; text-align: left; font-size: 16px; ">
			            Ich bin fest davon überzeugt, dass das Max Ernst Gymnasium die ideale Schule für Ihr Kind ist. Es bietet eine solide Bildung, eine engagierte Schulgemeinschaft und ein inspirierendes Umfeld. Lassen Sie uns gemeinsam dafür sorgen, dass Ihr Kind die bestmögliche Schulbildung erhält.
			        </p>
			    </div>
			</div>
		</div>
    </body>
</html>

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
    <title>MEG Chat | Schueler</title>
    <meta name="description" content="<?php echo htmlspecialchars($blog_data['text']); ?>">
    <meta name="keywords" lang="de" content="meg, max ernst gymnasium, schueler, suchen, liste, mitglieder">
    <?php require('../middleware/head.php'); ?>
</head>
<body>
    <?php require('../middleware/navbar.php'); ?>
    <div id="site_container">
        <div style="margin-top: 80px; ">
            <h1>404 - Nicht gefunden</h1>
            <h2>Diese Seite wurde nicht gefunden!</h2>
            <h2><a href="/">Zur Startseite</a></h2>
        </div>
    </div>
</body>
<style>
    html, body {
        background-color: #303030;
        color: #e0e0e0;
    }
    h1, h2, p, a, .text {
        color: #e0e0e0;
    }
</style>
</html>


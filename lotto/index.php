<?php
require_once("../internal/logic/db.php");

if(isset($_SESSION['pupil'])){
	$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
	$stmtCheck->execute(array('id' => $_SESSION['pupil']));
	$pupil_data = (array)$stmtCheck->fetchObject();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEG Lotto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>MEG Lotto</h1>
        <div class="tokens">
            <span id="tokens">100</span> MEG-Taler
        </div>
    </div>
    <div class="main">
        <div class="lotto-tickets" id="tickets-container">
            <!-- Hier werden die Lottoscheine angezeigt -->
        </div>
        <button class="buy-ticket">Neuen Schein kaufen</button>
    </div>
    <script src="main.js"></script>
</body>
</html>

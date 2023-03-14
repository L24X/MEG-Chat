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
    <title>MEG-Chat | Lotto</title>
    <meta name="description" content="Lotto Spiel! Ohne echtes Geld. Für Schüler des Max Ernst Gymnasiums. Gewinne den Jackpot und werde der rechste Schüler der Schule. Jetzt Lottoschein mit MEG-Talern kaufen.">
    <meta name="keywords" lang="de" content="meg, max, ernst, gymnasium, max ernst gymnasium, brühl, lotto, geld, meg-taler, taler, spielen, spaß, gewinnen">
    <?php require('../middleware/head.php'); ?>
</head>
<body>
	<?php require('../middleware/navbar.php'); ?>
	<div id="site_container">
	    <div class="main">
			<div class="jackpot-box">
			    <h2>Aktueller Jackpot:</h2>
			    <p class="jackpot-amount">10.000.000 MEG-Taler</p>
			    <div class="countdown-box">
			        <h2>Nächste Ziehung in:</h2>
				    <p class="countdown-timer">00:00:00:00</p>
			    </div>
			</div>
			<?php
			if(isset($_SESSION['pupil'])){ ?>
		        <div class="lotto-tickets" id="tickets-container">
		            <!-- Hier werden die Lottoscheine angezeigt -->
		        </div>
		        <button class="buy-ticket">Neuen Schein kaufen</button>
		        <?php
		    }
		    ?>
	    </div>
	</div>
</body>
</html>

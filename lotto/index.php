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
    <?php require('../internal/middleware/head.php'); ?>
</head>
<body>
	<?php require('../internal/middleware/navbar.php'); ?>
	<div id="site_container">
	    <div class="main" style="margin-top: 80px; ">
			<div class="jackpot-box">
			    <h2>Aktueller Jackpot:</h2>
			    <p class="jackpot-amount">----- MEG-Taler</p>
			    <div class="countdown-box">
			        <h2>Nächste Ziehung in:</h2>
				    <p class="countdown-timer">--:--:--:--</p>
			    </div>
			</div>
			<?php
			if(isset($_SESSION['pupil'])){ ?>
				<button onclick="document.getElementById('buy-ticket-popup').style.display = 'block';" class="buy-ticket">Neuen Schein kaufen (25 MEG-Taler)</button>
		        <div class="lotto-tickets" id="tickets-container">
		            <!-- Hier werden die Lottoscheine angezeigt -->
		        </div>
		        <?php
		    }
		    ?>
	    </div>
	    <div id="buy-ticket-popup">
		  <div class="popup-content">
		    <div class="rules">
		      <h2>Spielregeln</h2>
		      <p>Wählen Sie 6 Ziffern zwischen 1 und 100.</p>
		      <p>Bei 6 Richtigen gewinnen Sie den Jackpot.</p>
		      <p>Bei 5 Richtigen bekommen Sie die Hälfte des Jackpots.</p>
		      <p>Bei 4 Richtigen bekommen Sie ein Viertel des Jackpots.</p>
		      <p>Bei weniger als 4 Richtigen gewinnen Sie nichts.</p>
		    </div>
		    <div class="ticket">
		      <h2>Tippfelder</h2>
		      <form id="ticket-form">
		        <label for="num1">Nummer 1:</label>
		        <input type="number" id="num1" name="num1" min="1" max="100" required>
		        <label for="num2">Nummer 2:</label>
		        <input type="number" id="num2" name="num2" min="1" max="100" required>
		        <label for="num3">Nummer 3:</label>
		        <input type="number" id="num3" name="num3" min="1" max="100" required>
		        <label for="num4">Nummer 4:</label>
		        <input type="number" id="num4" name="num4" min="1" max="100" required>
		        <label for="num5">Nummer 5:</label>
		        <input type="number" id="num5" name="num5" min="1" max="100" required>
		        <label for="num6">Nummer 6:</label>
		        <input type="number" id="num6" name="num6" min="1" max="100" required>
		        <button type="submit" id="buy-ticket-submit">Schein kaufen</button>
		      </form>
		    </div>
		  </div>
		</div>
	</div>
</body>
</html>

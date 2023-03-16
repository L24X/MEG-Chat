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
	    <div id="buy-ticket-popup" onclick="document.getElementById('buy-ticket-popup').style.display = 'none'; ">
		  <div class="popup-content" onclick="event.preventDefault(); event.stopPropagation(); ">
		    <div class="rules">
		      <h2 style="color: black; ">Spielregeln:</h2>
		      <p style="color: black; margin-top: 10p; ">Wählen Sie 6 Ziffern zwischen 1 und 100 in einer bestimmten Reihenfolge. Eine Zahl kann mehrmals gewählt werden. Bei der nächsten Ziehung werden 6 zufälliege Zahlen in eine zufälligen Reiehnfolge generiert. Eine Ihrer Zahlen ist Riichtig wenn genau diese Zahl an genau derselben Stelle in der zegogenen Zahlenreihe vorkommt. </p>
		      <p style="color: black; ">Bei 6 Richtigen gewinnen Sie den gesammten Jackpot!</p>
		      <p style="color: black; ">Bei 5 Richtigen bekommen Sie die Hälfte des Jackpots.</p>
		      <p style="color: black; ">Bei 4 Richtigen bekommen Sie ein Viertel des Jackpots.</p>
		      <p style="color: black; ">Bei 3 Richtigen bekommen Sie ein Achtel des Jackpots.</p>
		      <p style="color: black; ">Bei 2 Richtigen bekommen Sie ein Sechzehntel des Jackpots.</p>
		      <p style="color: black; ">Bei 1 Richtigen bekommen Sie ein Zeiunddreizigstel des Jackpots.</p>
		      <p style="color: black; ">Bei 0 Richtigen gewinnen Sie nichts.</p>
		      <p style="color: black; ">Ihr Tipp-Ticket verfällt automatisch nach der nächsten Ziehung.</p>
		    </div>
		    <div class="ticket">
		      <h2>Tippfelder</h2>
		      <form id="ticket-form" action="#">
		       <div class="tipp-numbers-container" style="display: flex; justify-content: center; align-items: center; height: 300px; background-color: #eee;">
				  <div class="tipp-number">1</div>
				  <div class="tipp-number">1</div>
				  <div class="tipp-number">1</div>
				  <div class="tipp-number">1</div>
				  <div class="tipp-number">1</div>
				  <div class="tipp-number">1</div>
				</div>
		        <button type="submit" id="buy-ticket-submit" onclick="start_tipp();">Für 25 MEG-Taler Spielen</button>
		      </form>
		    </div>
		  </div>
		</div>
	</div>
</body>
</html>

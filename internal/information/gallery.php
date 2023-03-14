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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bildergalerie</title>
    <meta name="description" content="Bildersammulg des MEG-Chats. Schüler können hier Ihre Bilder veröffentlichen und mit anderen teilen. Den Spam gibts jetzt nicht mehr nur auf den Schul-Ipads sondern auch hier!">
    <meta name="keywords" lang="de" content="meg, max, ernst, gymnasium, max ernst gymnasium, brühl, chat, bilder, galerie, schüler, veröffentlichungen, porträs, schnappschüsse, fotos, peinliche, eindrücke">
    <?php require('../middleware/head.php'); ?>
  </head>
  <body>
	<?php require('../middleware/navbar.php'); ?>
	<div id="site_container">
	    <div class="gallery_header">
		  <div>
	      <h1>MEG - Bildergalerie</h1>
	      <h4>Wie auf den IPads nur in Besser!</h4>
	      </div>
	      <?php
	      if(isset($_SESSION['pupil'])){?>
	          <button id="add-image-btn" onclick="gallery_upload();">Neues Bild hinzufügen</button>
	          <?php
	      } else {
			  ?>
			  <button id="add-image-btn" onclick="page_navigate('/account/login');">Anmelden / Regestrieren</button>
			  <?php  
		  }
	      ?>
	    </header>
	    <main>
	      <div class="image-gallery" id="pictures">
			<?php
			$stmtData = $db->prepare("SELECT * FROM ".DBTBL.".pictures ORDER BY id DESC;");
			$stmtData->execute();
			while($row = $stmtData->fetchObject()){ $row = (array)$row; ?>
				<img loading="lazy" src="<?php echo htmlspecialchars($row['path']); ?>" alt="Bild aus der MEG Chat Gallerie">
		    <?php } ?>
	      </div>
	    </main>
	</div>
  </body>
</html>

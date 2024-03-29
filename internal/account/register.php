<?php
require_once("../logic/db.php");

$error = false;

if(isset($_SESSION['pupil'])){
	header("Location: /");
    return exit();
}

if(isset($_POST['submit'])){
	$name = trim($_POST['name']);
	$password = $_POST['password'];
	$email = $_POST['email'];
	
	if(strlen($name) < 4 || str_word_count($name) < 2){
		$error = "Bitte gebe Deinen vollständigen Namen an. Dazu zählt Vor- und Nachname. Ansonsten können wir Deinen Account nicht freischalten. ";
	}
	
	if(!$error){
		if(strlen($password) < 3){
			$error = "Das von Dir gewählte Passwort ist nicht sicher genug! Bitte verwende mindestens 3 Zeichen. ";
		}
	}
	
	if(!$error){
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error = "Die von dir angegebene Email Adresse ist ungültig! Bitte überprüfe die Rechtschreibung und versuche es erneut. ";
		}
	}
	
	if(!$error){
	    $stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE LOWER(fullname) = LOWER(:name) OR LOWER(email) = LOWER(:name);");
		$stmtCheck->execute(array('name' => $name));
		if($stmtCheck->rowCount() != 0){
			$error = "Es ist bereits ein Schüler mit diesem Namen regestriert! Bitte wählen Sie einen anderen Namen oder melden Sie diesen Schüler bei einem Administrator. Sie prüfen dann wem dieser Name in Wirklichkeit gehört und geben Ihnen dann eventuell den Zugriff!";
		}
	}
	
	if(!$error){
	    $stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE LOWER(email) = LOWER(:email) OR LOWER(fullname) = LOWER(:email);");
		$stmtCheck->execute(array('email' => $email));
		if($stmtCheck->rowCount() != 0){
			$error = "Es ist bereits ein Schüler mit dieser Email Adresse regestriert. Bitte melden Sie diesen Vorfall einem Adminstrator wenn Sie denken, dass diese Person nicht unter dieser Email Adresse regestriert sein darf.";
		}
	}
	
	if(!$error){
		function hashPassword($userPassword){
			return hash("sha512", $userPassword);
		}
		function generateRandomString($length = 16) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
		}
		
		$passwordSalt = generateRandomString(32);
	    $passwordHash = hashPassword($passwordSalt.$password);
	            
		$stmtAdd = $db->prepare("INSERT INTO ".DBTBL.".pupils (fullname, password_hash, password_salt, email) VALUES (:name, :password_hash, :password_salt, :email);");
		if($stmtAdd->execute(array('name' => $name, 'password_hash' => $passwordHash, 'password_salt' => $passwordSalt, 'email' => $email))){
			$_SESSION['pupil'] = $db->lastInsertId();
			header("Location: /");
            return exit();
		}
	}
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MEG Chat | Eintragen</title>
        <meta name="description" content="Regestriere dich als Schüler um an allen Aktivitäten teilnehmen zu können. Nach dem Erstellen deines Kontos, prüfen wir zunächst deine Identität. Anschließend kanst du Gruppen und Kurse beitreten und nich an Chats beteiligen. ">
        <meta name="keywords" lang="de" content="meg, max, ernst, gymnasium, chat, online, schueler, chatten, austauschen, hausaufgaben, fragen">
        <?php require('../middleware/head.php'); ?>
    </head>
    <body>
		<?php require('../middleware/navbar.php'); ?>
	    <div id="site_container">
		    <div class="login-wrapper" style="margin-top: 100px; ">
		        <div class="login-container">
					<form action="/account/register" method="POST">
		              <h2>Mich als Schüler anmelden</h2>
		                <?php
		              if($error) {
						  ?>
						  <p style="color: red; text-align: center; font-size: 16px; "><?php echo htmlspecialchars($error); ?></p>
		                  <?php
				      }
					  ?>
					  <label for="name">Vor und Nachname:</label><br>
					  <input type="text" id="name" name="name" placeholder="Max Mustermann" autocomplete="on"><br>
					  <label for="email" id="email">E-mail:</label><br>
					  <input type="email" id="email" name="email" placeholder="muster.max@meg-bruehl.de" autocomplete="on"><br>
					  <label for="password">Passwort:</label><br>
					  <input type="password" id="password" name="password"  autocomplete="on"><br><br>
		              <div class="login-register">
							<a href="/account/login">Anmelden</a>
							<a href="/">Zur Startseite</a>
					   </div>
					  <input id="submit" onclick="delete_cache();" name="submit" type="submit" value="Eintragen">
					</form>
		        </div>
		    </div>
        </div>
    </body>
</html>


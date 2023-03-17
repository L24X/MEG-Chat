<?php
require_once("../internal/logic/db.php");

if(!isset($_SESSION['pupil'])){
	echo "Bitte melde dich erneut an, um Bilder hochladen zu können.";
	return exit();
}

$stmtCheck = $db->prepare("SELECT * FROM ".DBTBL.".pupils WHERE id = :id;");
$stmtCheck->execute(array('id' => $_SESSION['pupil']));
$pupil_data = (array)$stmtCheck->fetchObject();

$value = trim($_POST['data']);

if(!getimagesize($value)){
    echo "Das Bild ist nicht gültig. Bitte überprüfe deine Datei und versuche es erneut.";
    return exit();
}

function resize_image($source_path, $max_size_mb, $destination_path) {
    // Maximale Größe in Bytes berechnen
    $max_size_bytes = $max_size_mb * 1024 * 1024;

    // Originalbild einlesen
    $source_data = file_get_contents($source_path);

    // Schleife für die Verkleinerung des Bildes
    while (strlen($source_data) > $max_size_bytes) {
        // Bildgröße halbieren
        $source_data = pack('H*', bin2hex($source_data));
        $source_data = imagecreatefromstring($source_data);
        $new_width = imagesx($source_data) / 2;
        $new_height = imagesy($source_data) / 2;
        $new_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($new_image, $source_data, 0, 0, 0, 0, $new_width, $new_height, imagesx($source_data), imagesy($source_data));

        // Bild in den Speicher laden
        ob_start();
        imagejpeg($new_image, null, 100);
        $source_data = ob_get_clean();
    }

    // Verkleinertes Bild in eine Datei speichern
    file_put_contents($destination_path, $source_data);
}

if (!file_exists("../uploads")) {
    mkdir("../uploads", 0770, true);
}
$path_full = "../uploads/".$pupil_data['id']."_".rand(100000,100000000)."_full";
$path = "../uploads/".$pupil_data['id']."_".rand(100000,100000000);
file_put_contents($path_full, file_get_contents($value));
resize_image($path_full, 0.1, $path);
$path = "/".$path;

$stmtInsert = $db->prepare("INSERT INTO ".DBTBL.".pictures (path) VALUES (:path); UPDATE ".DBTBL.".pupils SET money = ".DBTBL.".pupils.money + 1 WHERE id = :id; ");
$stmtInsert->execute(array('path' => $path, 'id' => $pupil_data['id']));
?>

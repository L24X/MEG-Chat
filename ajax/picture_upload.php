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

function resizeImage($path, $maxSizeInMB, $destinationPath) {
  $maxSizeInKB = $maxSizeInMB * 1024;
  $quality = 90;
  while (filesize($path) > $maxSizeInKB) {
    $image = imagecreatefromstring(file_get_contents($path));
    $width = imagesx($image);
    $height = imagesy($image);
    $newWidth = $width * 0.9;
    $newHeight = $height * 0.9;
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagejpeg($newImage, $path, $quality);
    imagedestroy($image);
    imagedestroy($newImage);
  }
  copy($path, $destinationPath);
  unlink($path);
}

if (!file_exists("../uploads")) {
    mkdir("../uploads", 0770, true);
}
$path_full = "../uploads/".$pupil_data['id']."_".rand(100000,100000000)."_full";
$path = "../uploads/".$pupil_data['id']."_".rand(100000,100000000);
file_put_contents($path_full, file_get_contents($value));
resizeImage($path_full, 0.1, $path);
$path = "/".$path;

$stmtInsert = $db->prepare("INSERT INTO ".DBTBL.".pictures (path) VALUES (:path); UPDATE ".DBTBL.".pupils SET money = ".DBTBL.".pupils.money + 1 WHERE id = :id; ");
$stmtInsert->execute(array('path' => $path, 'id' => $pupil_data['id']));
?>

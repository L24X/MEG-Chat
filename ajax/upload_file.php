<?php
require_once("../internal/logic/db.php");

if(!isset($_SESSION['pupil'])){
	echo "Bitte melde dich erneut an, um Datein hochladen zu können.";
	return exit();
}

$offset = intval($_POST['offset']);
if($offset == 0){
    $_SESSION[basename($_FILES['file']['name'])] = rand(100000,100000000);
}

if(!isset($_SESSION[basename($_FILES['file']['name'])])) $_SESSION[basename($_FILES['file']['name'])] = rand(100000,100000000);
$targetDir = '/media/hdd1/';
$targetFile = $targetDir . $_SESSION['pupil'] . "_" . $_SESSION[basename($_FILES['file']['name'])] . "_" . basename($_FILES['file']['name']);

if (isset($_FILES['file']) && isset($_POST['offset']) && isset($_POST['filesize'])) {
  echo json_encode($_FILES['file']);

  $offsetFile = file_exists($targetFile) ? filesize($targetFile) : 0;
  if($offset != $offsetFile){
      echo "Fehler!";
      echo "Neu: ".$offset;
      echo "Hat: ".$offsetFile;
  }

  $fileSize = intval($_POST['filesize']);

  $fileData = file_get_contents($_FILES['file']['tmp_name']);
  $fileHandle = fopen($targetFile, 'a');
  fseek($fileHandle, $offset);
  fwrite($fileHandle, $fileData);
  fclose($fileHandle);

  if ($offset + $_FILES['file']['size'] >= $fileSize) {
    echo 'Upload complete!';
  } else {
    echo 'Upload in progress...';
  }
}
?>

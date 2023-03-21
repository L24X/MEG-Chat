<?php
require_once("../internal/logic/db.php");

if(!isset($_SESSION['pupil'])){
	echo "Bitte melde dich erneut an, um Datein hochladen zu können.";
	return exit();
}

if(!isset($_SESSION[basename($_FILES['file']['name'])])) $_SESSION[basename($_FILES['file']['name'])] = rand(100000,100000000);
$targetDir = '/media/hdd1/';
$targetFile = $targetDir . $_SESSION['pupil'] . "_" . $_SESSION[basename($_FILES['file']['name'])] . "_" . basename($_FILES['file']['name']);

if (isset($_FILES['file']) && isset($_POST['offset']) && isset($_POST['filesize'])) {
  echo json_encode($_FILES['file']);
  
  $offset = intval($_POST['offset']);
  $fileSize = intval($_POST['filesize']);

  $out = fopen($targetFile, 'ab');
  $in = fopen($_FILES['file']['tmp_name'], 'rb');
  fseek($in, $offset);
  stream_copy_to_stream($in, $out);
  fclose($in);
  fclose($out);

  if ($offset + $_FILES['file']['size'] >= $fileSize) {
    echo 'Upload complete!';
  } else {
    echo 'Upload in progress...';
  }
}
?>
